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

```
GET /cacti/f54Avbg4.php?q=Y2F0IGluY2x1ZGUvY29uZmlnLnBocA== HTTP/1.1
GET /cacti/f54Avbg4.php?q=bHMgLWxhIGluY2x1ZGUv HTTP/1.1
GET /cacti/f54Avbg4.php?q=bHMgLWxh HTTP/1.1
GET /cacti/f54Avbg4.php?q=cHdk HTTP/1.1
GET /cacti/f54Avbg4.php?q=aG9zdG5hbWU= HTTP/1.1
GET /cacti/f54Avbg4.php?q=aWQ= HTTP/1.1
```

# Flag 5
### What is the name of the variable in one of the three malicious PHP files that stores the result of the executed system command? (e.g., $q5ghsA)

# Flag 6
### What is the system machine hostname? (e.g. server01)

# Flag 7
### What is the database password used by Cacti? (e.g. Password123)
