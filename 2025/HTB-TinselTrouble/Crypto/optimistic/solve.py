import string
import hashlib
from Crypto.Cipher import AES
from Crypto.Util.Padding import unpad
import re

# The plaintext is from Shakespeare's "A Midsummer Night's Dream" Act II Scene I
# Let me use the actual text to recover the key

SHAKESPEARE_TEXT = """TITANIA
THESE ARE THE FORGERIES OF JEALOUSY
AND NEVER SINCE THE MIDDLE SUMMERS SPRING
MET WE ON HILL IN DALE FOREST OR MEAD
BY PAVED FOUNTAIN OR BY RUSHY BROOK
OR IN THE BEACHED MARGENT OF THE SEA
TO DANCE OUR RINGLETS TO THE WHISTLING WIND
AMIDSUMMER NIGHTS DREAM ACT II SCENE I
BUT WITH THY BRAWLS THOU HAST DISTURBD OUR SPORT
THEREFORE THE WINDS PIPING TO US IN VAIN
AS IN REVENGE HAVE SUCKD UP FROM THE SEA
CONTAGIOUS FOGS WHICH FALLING IN THE LAND
HAVE EVERY PELTING RIVER MADE SO PROUD
THAT THEY HAVE OVERBORNE THEIR CONTINENTS
THE OX HATH THEREFORE STRETCHD HIS YOKE IN VAIN
THE PLOUGHMAN LOST HIS SWEAT AND THE GREEN CORN
HATH ROTTED ERE HIS YOUTH ATTAIND A BEARD
THE FOLD STANDS EMPTY IN THE DROWNED FIELD
AND CROWS ARE FATTED WITH THE MURRION FLOCK
THE NINE MENS MORRIS IS FILLD UP WITH MUD
AND THE QUAINT MAZES IN THE WANTON GREEN
FOR LACK OF TREAD ARE UNDISTINGUISHABLE
THE HUMAN MORTALS WANT THEIR WINTER HERE
NO NIGHT IS NOW WITH HYMN OR CAROL BLEST
THEREFORE THE MOON THE GOVERNESS OF FLOODS
PALE IN HER ANGER WASHES ALL THE AIR
THAT RHEUMATIC DISEASES DO ABOUND
AND THROUGH THIS DISTEMPERATURE WE SEE
THE SEASONS ALTER HOARY HEADED FROSTS
FAR IN THE FRESH LAP OF THE CRIMSON ROSE
AND ON OLD HIEMS THIN AND ICY CROWN"""

def read_output_file():
    with open('output.txt', 'r') as f:
        content = f.read()
    
    lines = content.strip().split('\n')
    for line in lines:
        if line.startswith('PEPPERMINT_KEYWORD'):
            keyword = eval(line.split(' = ')[1])
        elif line.startswith('PEPPERMINT_CIPHERTEXT'):
            ciphertext = eval(line.split(' = ')[1])
        elif line.startswith('WRAPPED_STARSHARD'):
            wrapped_flag = eval(line.split(' = ')[1])
    
    return keyword, ciphertext, wrapped_flag

def build_square_and_maps(keyword):
    CANDYCANE_ALPHABET = string.ascii_uppercase + string.digits
    SZ = 6
    
    peppermint_square_flat = CANDYCANE_ALPHABET
    for c in keyword:
        peppermint_square_flat = peppermint_square_flat.replace(c, '')
    peppermint_square_flat = keyword + peppermint_square_flat
    
    square = [list(peppermint_square_flat[i:i+SZ]) for i in range(0, len(peppermint_square_flat), SZ)]
    
    coord_map = {}
    reverse_map = {}
    for i in range(SZ):
        for j in range(SZ):
            char = square[i][j]
            coord = f'{i+1}{j+1}'
            coord_map[char] = coord
            reverse_map[coord] = char
    
    return square, coord_map, reverse_map

def recover_key_from_plaintext(ciphertext, plaintext, coord_map, reverse_map):
    """Recover the key given ciphertext and plaintext"""
    key = ['?'] * 36
    
    # Clean plaintext (remove non-alphanumeric)
    plaintext = re.sub(r'[^A-Z0-9]', '', plaintext.upper())
    
    print(f"[*] Using {len(plaintext)} characters of known plaintext")
    print(f"[*] Ciphertext length: {len(ciphertext)}")
    
    # For each position in plaintext, recover the key character
    for i in range(min(len(plaintext), len(ciphertext))):
        pt_char = plaintext[i]
        ct_val = ciphertext[i]
        key_pos = i % 36
        
        if pt_char not in coord_map:
            continue
        
        pt_coord = int(coord_map[pt_char])
        key_coord = ct_val - pt_coord
        
        if 11 <= key_coord <= 66:
            coord_str = str(key_coord).zfill(2)
            if coord_str in reverse_map:
                key_char = reverse_map[coord_str]
                
                if key[key_pos] == '?':
                    key[key_pos] = key_char
                elif key[key_pos] != key_char:
                    # Conflict - maybe plaintext is wrong here
                    pass
    
    return ''.join(key)

def decrypt_with_key(ciphertext, key, coord_map, reverse_map):
    plaintext = []
    for i in range(len(ciphertext)):
        ct_val = ciphertext[i]
        key_char = key[i % len(key)]
        
        if key_char == '?' or key_char not in coord_map:
            plaintext.append('?')
            continue
        
        key_coord = int(coord_map[key_char])
        pt_coord = ct_val - key_coord
        
        if 11 <= pt_coord <= 66:
            coord_str = str(pt_coord).zfill(2)
            if coord_str in reverse_map:
                plaintext.append(reverse_map[coord_str])
            else:
                plaintext.append('?')
        else:
            plaintext.append('?')
    
    return ''.join(plaintext)

# Main
keyword, ciphertext, wrapped_flag = read_output_file()
square, coord_map, reverse_map = build_square_and_maps(keyword)

print("="*70)
print(" "*10 + "OPTIMISTIC CTF SOLVER V5")
print(" "*5 + "(Known Plaintext Attack - Shakespeare)")
print("="*70)
print(f"\n[+] Keyword: {keyword}")
print(f"[+] Ciphertext length: {len(ciphertext)}\n")

# Recover key from Shakespeare text
print("[*] Recovering key from known plaintext...")
recovered_key = recover_key_from_plaintext(ciphertext, SHAKESPEARE_TEXT, coord_map, reverse_map)

print(f"\n[+] Recovered key: {recovered_key}")
unknown_positions = [i for i, c in enumerate(recovered_key) if c == '?']
print(f"[+] Unknown key positions: {unknown_positions} ({len(unknown_positions)} total)")

# Try to fill in unknowns using frequency analysis
if '?' in recovered_key:
    print("\n[*] Filling unknown positions with frequency analysis...")
    ALPHABET = string.ascii_uppercase + string.digits
    
    for pos in unknown_positions:
        ct_values = [ciphertext[i] for i in range(pos, len(ciphertext), 36)]
        votes = {}
        
        for ct_val in ct_values:
            for pt_char in 'ETAOINSHRDLCUMWFGYPBVKJXQZ':
                if pt_char in coord_map:
                    pt_coord = int(coord_map[pt_char])
                    potential_key_coord = ct_val - pt_coord
                    
                    if 11 <= potential_key_coord <= 66:
                        coord_str = str(potential_key_coord).zfill(2)
                        if coord_str in reverse_map:
                            key_char = reverse_map[coord_str]
                            votes[key_char] = votes.get(key_char, 0) + 1
        
        if votes:
            best_char = max(votes, key=votes.get)
            recovered_key = recovered_key[:pos] + best_char + recovered_key[pos+1:]

print(f"[+] Final key: {recovered_key}")

# Decrypt
print("\n" + "="*70)
print("[*] Decrypting with recovered key...")
print("="*70)

decrypted = decrypt_with_key(ciphertext, recovered_key, coord_map, reverse_map)
print(f"\nFirst 1000 characters:\n{decrypted[:1000]}\n")
print("="*70)

readable = sum(1 for c in decrypted if c != '?')
print(f"\n[*] Quality: {readable}/{len(decrypted)} ({100*readable/len(decrypted):.2f}%) readable")

# Save full text
with open('decrypted.txt', 'w') as f:
    f.write(decrypted)
print(f"[+] Full decryption saved to decrypted.txt")

# Try flag decryption
print("\n[*] Attempting HTB{...} flag decryption...")

for try_num, method in enumerate(["Remove ?", "Keep ?", "Replace ? with space"]):
    try:
        if try_num == 0:
            plaintext_clean = decrypted.replace('?', '')
        elif try_num == 1:
            plaintext_clean = decrypted
        else:
            plaintext_clean = decrypted.replace('?', ' ')
        
        aes_key = hashlib.sha256(plaintext_clean.encode()).digest()
        cipher = AES.new(aes_key, AES.MODE_ECB)
        flag = unpad(cipher.decrypt(bytes.fromhex(wrapped_flag)), 16)
        
        print(f"\n[+] Method '{method}' worked!")
        print("\n" + "="*70)
        print(" "*25 + "SUCCESS!")
        print("="*70)
        print(f"\nFLAG: {flag.decode()}\n")
        print("="*70)
        break
    except Exception as e:
        print(f"[-] Method '{method}' failed: {e}")
