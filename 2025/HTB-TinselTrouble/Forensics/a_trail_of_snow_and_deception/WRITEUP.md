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

# Flag 4
### What file gets downloaded using curl during exploitation process? (e.g. filename)

# Flag 5
### What is the name of the variable in one of the three malicious PHP files that stores the result of the executed system command? (e.g., $q5ghsA)

# Flag 6
### What is the system machine hostname? (e.g. server01)

# Flag 7
### What is the database password used by Cacti? (e.g. Password123)
