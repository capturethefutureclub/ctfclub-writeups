

# Tinselwick Investigation - CTF Writeup
> The Tinselwick Behavioral Records Authority has detected suspicious
> modifications to their Naughty & Nice database just weeks before
> Christmas Eve. Senior Archivist Frost suspects an insider
> breach—someone has tampered with behavioral records, audit logs, and
> registry entries in an attempt to alter the Nice List. She needs a
> skilled investigator to examine the TinselOS archives, compare current
> records against historical snapshots, trace unauthorized changes
> through the audit trail, and uncover the compromised CHILD_ID before
> Santa's final review. Flag Format: HTB{CHILD_ID} Example:

### Step 1: Play through the browser
### Step 2: Click the "Flag Format" button on the Upper Right.

<img width="742" height="309" alt="image" src="https://github.com/user-attachments/assets/b626a16a-02a7-455e-b4b7-89da4cf3936a" />

### Step 3: Copy the Example Flag
The example flag **`HTB{TC-5503}`** is actually the Flag for this Task.

<img width="753" height="471" alt="image" src="https://github.com/user-attachments/assets/f04b29ef-da08-4a53-a16f-8ebcbe49846b" />

---
However, there are more ways to find the flag.
### Step 1: Inside the website, Click "Change Detector"
### Step 2: Compare Dec. 15, 18, or 21 to Dec. 22
Notice that `TC-5503 - Bramble Shadowpine` was the only one changed from **NAUGHTY** to **NICE**.

<img width="794" height="219" alt="image" src="https://github.com/user-attachments/assets/dc022067-1b15-47aa-92a4-29cd5447ccce" />

And you will also see this changes in the "Audit Log"
<img width="712" height="147" alt="image" src="https://github.com/user-attachments/assets/e93f0458-acc0-4d7d-ad94-a2879713f27d" />

Since the Flag Format is `HTB{CHILD_ID}`, Replace the CHILD_ID with `TC-5503`.

**Flag: `HTB{TC-5503}`**
