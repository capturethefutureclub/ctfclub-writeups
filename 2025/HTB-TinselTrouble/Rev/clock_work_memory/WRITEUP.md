Here is the final `writeup.md` content:
# Clock Work Memory – CTF Reverse Engineering Challenge Writeup

---

This challenge gives a single file: `pocketwatch.wasm`, a small WebAssembly (WASM) binary of 410 bytes. The story says Twillie’s clockwork pocketwatch has a distorted memory about the Starshard, and you must reverse‑engineer the “clockwork” mechanism and use the correct “peppermint key” to recover the truth (the flag). [web:21][web:18]

The task is to:
- Understand what the WASM module does.
- Find how the memory is “distorted.”
- Recover the original string (flag) from the obfuscated data.

---

## Step 1 – Quick Recon

List files and confirm type:

```bash
ls -la
file pocketwatch.wasm
```

Output:

```text
-rwxr-xr-x 1 zero zero 410 Dec 21 03:24 pocketwatch.wasm
pocketwatch.wasm: WebAssembly (wasm) binary module version 0x1 (MVP)
```

So it’s a tiny, valid WebAssembly module.

Extract strings:

```bash
strings pocketwatch.wasm
```

You see interesting exports:

```text
memory
check_flag
__indirect_function_table
_initialize
_emscripten_stack_restore
emscripten_stack_get_current
```

`check_flag` clearly looks like the verification function we care about.

---

## Step 2 – Decompile WASM to WAT

Use WABT’s `wasm2wat` to get text format. [web:21][web:18]

```bash
wasm2wat pocketwatch.wasm -o pocketwatch.wat
cat pocketwatch.wat
```

Relevant parts of the output:

```wat
(module
  (type (;0;) (func))
  (type (;1;) (func (param i32) (result i32)))
  ...
  (func (;1;) (type 1) (param i32) (result i32)
    (local i32 i32 i32 i32)
    global.get 0
    i32.const 32
    i32.sub
    local.tee 2
    global.set 0
    local.get 2
    i32.const 1262702420
    i32.store offset=27 align=1
    loop  ;; label = @1
      local.get 1
      local.get 2
      i32.add
      local.get 2
      i32.const 27
      i32.add
      local.get 1
      i32.const 3
      i32.and
      i32.add
      i32.load8_u
      local.get 1
      i32.load8_u offset=1024
      i32.xor
      i32.store8
      local.get 1
      i32.const 1
      i32.add
      local.tee 1
      i32.const 23
      i32.ne
      br_if 0 (;@1;)
    end
    local.get 2
    i32.const 0
    i32.store8 offset=23
    ;; then: compare buffer with input string and return 1 if equal
    ...
  )
  ...
  (memory (;0;) 258 258)
  ...
  (export "memory" (memory 0))
  (export "check_flag" (func 1))
  ...
  (data (;0;) (i32.const 1024) "\1c\1b\010#{0&\0b=p=\0b~0\147\7fs'un>")
)
```

Key observations:

- `check_flag` is exported and takes one parameter (pointer to input string) and returns an `i32`.
- It allocates 32 bytes on the stack and uses `local 2` as a buffer.
- It stores a constant `1262702420` into the buffer at offset 27.
- It loops 23 times, XORing bytes from memory starting at 1024 with a 4‑byte repeating key.
- After the loop, it compares the decrypted buffer with the input string and returns whether they match.

In plain English:

> `check_flag` decrypts 23 bytes starting at memory address 1024 with a 4‑byte key, stores the result in a local buffer, null‑terminates it, and then compares that result to the string you pass as parameter. If they match, the flag is correct.

---

## Step 3 – Recover the XOR Key (“TOCK”)

The critical line is:

```wat
i32.const 1262702420
i32.store offset=27 align=1
```

This stores a 32‑bit integer into memory at `buffer + 27`.

Convert that constant:

```python
import struct

value = 1262702420
print(hex(value))                    # 0x4b434f54
print(struct.pack('<I', value))      # little-endian bytes
print(struct.pack('>I', value))      # big-endian bytes
```

Output:

```text
0x4b434f54
b'TOCK'
b'KCOT'
```

Interpreting the bytes:

- Hex: `0x4B 0x43 0x4F 0x54`
- Little‑endian bytes spell `"TOCK"`.

So the 4‑byte XOR key used in the loop is **"TOCK"** – on theme with the ticking pocketwatch.

The loop:

```wat
key_byte   = buffer[27 + (i & 3)]
data_byte  = memory[1024 + i]
buffer[i]  = key_byte XOR data_byte
```

And later the function compares `buffer` with the user’s input string. That means if we decrypt the data once ourselves, the result is exactly the flag string.

---

## Step 4 – Locate the Encrypted Data

The WAT shows a data segment:

```wat
(data (;0;) (i32.const 1024) "\1c\1b\010#{0&\0b=p=\0b~0\147\7fs'un>")
```

This means those bytes are placed at memory address 1024 when the module runs, but in the `.wasm` file they live at some file offset in the data section.

Use Python to search for the starting bytes:

```python
with open('pocketwatch.wasm', 'rb') as f:
    data = f.read()

target = b'\x1c\x1b\x01'
idx = data.index(target)
print("Found data at file offset:", idx)
segment = data[idx:idx+23]            # 23 bytes (as per loop bound)
print("Hex:", segment.hex())
print("Bytes:", list(segment))
```

Output:

```text
Found data at file offset: 387 (0x183)
Hex: 1c1b0130237b30260b3d703d0b7e3014377f7327756e3e
Bytes:[1][2][3]
```

These 23 bytes are the encrypted flag (`ciphertext`).

---

## Step 5 – Decrypt with the TOCK Key

We now know:

- Ciphertext:

```python
ciphertext = bytes([
    28, 27, 1, 48, 35, 123, 48, 38,
    11, 61, 112, 61, 11, 126, 48,
    20, 55, 127, 115, 39, 117, 110, 62
])
```

- Key: `"TOCK"` → `[84, 79, 67, 75]`.

The WASM loop does:

```text
plaintext[i] = ciphertext[i] XOR key[i mod 4]
```

### Decryption script

```python
ciphertext = bytes([
    28, 27, 1, 48, 35, 123, 48, 38,
    11, 61, 112, 61, 11, 126, 48,
    20, 55, 127, 115, 39, 117, 110, 62
])

# XOR key derived from 1262702420 (0x4B434F54 => "TOCK")
key = bytes()  # b"TOCK"

# XOR decryption: buffer[i] = ciphertext[i] ^ key[i % 4]
plaintext = bytes(
    c ^ key[i % len(key)]
    for i, c in enumerate(ciphertext)
)

print("Bytes:", plaintext)
print("As string:", plaintext.decode('utf-8', errors='replace'))
print("Hex:", plaintext.hex())
```

### How the script works

- `enumerate(ciphertext)` iterates over every encrypted byte, giving index `i` and value `c`.
- `i % len(key)` cycles through `0,1,2,3,0,1,2,3,...`, so the 4‑byte key repeats.
- `c ^ key[i % len(key)]` performs the XOR per byte, exactly like the WASM loop.
- `bytes(...)` collects all decrypted bytes into `plaintext`.
- `decode('utf-8')` converts the decrypted bytes into a readable string.

### Output

Running the script:

```text
Bytes: b'HTB{w4sm_r3v_1s_c00l!!}'
As string: HTB{w4sm_r3v_1s_c00l!!}
Hex: 4854427b7734736d5f7233765f31735f6330306c21217d
```

So the decrypted plaintext is the flag:

```text
HTB{w4sm_r3v_1s_c00l!!}
```

---

## Step 6 – Why Python and Possible Alternatives

Python is used here only as a convenient way to:

- Extract and slice raw bytes from the `.wasm`.
- Perform a repeating‑key XOR.
- Display the result in bytes, hex, and string form.

You could do the same with:

- A hex editor (select the 23‑byte range, XOR with `"TOCK"`).
- Radare2 or Ghidra scripting to XOR the data segment.
- Any other scripting language.

The core logic is always the same: repeat `"TOCK"` as a key and XOR with the 23‑byte ciphertext. [web:7][web:19]

---

FLAG
```text
HTB{w4sm_r3v_1s_c00l!!}
```