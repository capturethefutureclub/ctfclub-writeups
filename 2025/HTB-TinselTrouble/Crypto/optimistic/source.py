import string, re, random, hashlib
from secret import STARSHARD_SCROLL, FESTIVE_WHISPER_CLEAN, PEPPERMINT_KEYWORD
from Crypto.Util.Padding import pad
from Crypto.Cipher import AES

FESTIVE_WHISPER_CLEAN = re.sub(r'[^a-zA-Z0-9]', '', FESTIVE_WHISPER_CLEAN).upper()
CANDYCANE_ALPHABET = string.ascii_uppercase + string.digits
SZ = 6
L = SZ**2

def weave_peppermint_square():
    peppermint_square_flat = CANDYCANE_ALPHABET
    for c in PEPPERMINT_KEYWORD:
        peppermint_square_flat = peppermint_square_flat.replace(c, '')
    peppermint_square_flat = PEPPERMINT_KEYWORD + peppermint_square_flat
    return [list(peppermint_square_flat[i:i+SZ]) for i in range(0, len(peppermint_square_flat), SZ)]

peppermint_square = weave_peppermint_square()

BAUBLE_COORDS = {
    peppermint_square[i][j]: f'{i+1}{j+1}'
    for j in range(SZ)
    for i in range(SZ)
}

def swirl_encrypt(starstream_key, starlit_plaintext):
    twinkling_ct = []
    for i in range(len(starlit_plaintext)):
        key_off = int(BAUBLE_COORDS[starstream_key[i % len(starstream_key)]])
        pt_off = int(BAUBLE_COORDS[starlit_plaintext[i]])
        twinkling_ct.append(key_off + pt_off)
    return twinkling_ct

STARSTREAM_KEY = ''.join(random.sample(CANDYCANE_ALPHABET, k=L))
PEPPERMINT_CIPHERTEXT = swirl_encrypt(STARSTREAM_KEY, FESTIVE_WHISPER_CLEAN)

COCOA_AES_KEY = hashlib.sha256(FESTIVE_WHISPER_CLEAN.encode()).digest()
WRAPPED_STARSHARD = AES.new(COCOA_AES_KEY, AES.MODE_ECB).encrypt(pad(STARSHARD_SCROLL, 16)).hex()

open('output.txt', 'w').write(f'{PEPPERMINT_KEYWORD = }\n{PEPPERMINT_CIPHERTEXT = }\n{WRAPPED_STARSHARD = }')