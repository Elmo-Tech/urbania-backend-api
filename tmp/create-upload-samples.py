from datetime import datetime, timedelta, timezone
from pathlib import Path
import struct

from cryptography import x509
from cryptography.hazmat.primitives import hashes, serialization
from cryptography.hazmat.primitives.asymmetric import rsa
from cryptography.hazmat.primitives.serialization import pkcs7
from cryptography.x509.oid import NameOID


out = Path(__file__).resolve().parents[1] / 'output' / 'upload-samples'
out.mkdir(parents=True, exist_ok=True)

# A small Outlook MSG compound file. Use regular 4096-byte streams throughout,
# so this sample needs no mini-FAT. Seven children form a complete black tree.
strings = {
    0x001A: 'IPM.Note',
    0x0037: 'Urbania attachment upload test',
    0x1000: 'This is a sample Outlook message for testing attachment uploads.\r\nNo email was sent.\r\n',
    0x0C1A: 'Upload Test',
    0x0C1F: 'sender@example.com',
    0x0E04: 'Test Recipient <recipient@example.com>',
}
streams = {}
properties = bytearray(32)
properties += struct.pack('<IIII', 0x0E070003, 6, 1, 0)
properties += struct.pack('<IIII', 0x340D0003, 6, 0x40000, 0)
for prop_id, value in strings.items():
    data = (value + '\0').encode('utf-16le')
    streams[f'__substg1.0_{prop_id:04X}001F'] = data.ljust(4096, b'\0')
    properties += struct.pack('<IIII', (prop_id << 16) | 0x001F, 6, len(data), 0)
streams['__properties_version1.0'] = bytes(properties).ljust(4096, b'\0')
names = sorted(streams, key=lambda name: (len(name), name.upper()))
FREE, END, FAT = 0xFFFFFFFF, 0xFFFFFFFE, 0xFFFFFFFD

header = bytearray(512)
header[:8] = bytes.fromhex('D0CF11E0A1B11AE1')
struct.pack_into('<HHHHH', header, 24, 0x003E, 3, 0xFFFE, 9, 6)
struct.pack_into('<IIIIIIIII', header, 40, 0, 1, 1, 0, 4096, END, 0, END, 0)
struct.pack_into('<109I', header, 76, 0, *([FREE] * 108))

fat = [FREE] * 128
fat[0] = FAT
fat[1], fat[2] = 2, END
entries = []


def directory_entry(name, kind, left=FREE, right=FREE, child=FREE, start=END, size=0):
    entry = bytearray(128)
    encoded = (name + '\0').encode('utf-16le')
    entry[:len(encoded)] = encoded
    struct.pack_into('<HBBIII', entry, 64, len(encoded), kind, 1, left, right, child)
    struct.pack_into('<IQ', entry, 116, start, size)
    return bytes(entry)


entries.append(directory_entry('Root Entry', 5, child=4))
children = {2: (1, 3), 4: (2, 6), 6: (5, 7)}
for index, name in enumerate(names, 1):
    start = 3 + (index - 1) * 8
    for sector in range(start, start + 8):
        fat[sector] = sector + 1 if sector < start + 7 else END
    left, right = children.get(index, (FREE, FREE))
    entries.append(directory_entry(name, 2, left=left, right=right, start=start, size=4096))

(out / 'sample-outlook.msg').write_bytes(
    bytes(header) + struct.pack('<128I', *fat) + b''.join(entries)
    + b''.join(streams[name] for name in names)
)

# An attached CMS/PKCS#7 signature with a disposable, self-signed test certificate.
key = rsa.generate_private_key(public_exponent=65537, key_size=2048)
name = x509.Name([x509.NameAttribute(NameOID.COMMON_NAME, 'Urbania Upload Test Only')])
now = datetime.now(timezone.utc)
cert = (x509.CertificateBuilder().subject_name(name).issuer_name(name)
        .public_key(key.public_key()).serial_number(x509.random_serial_number())
        .not_valid_before(now - timedelta(minutes=1)).not_valid_after(now + timedelta(days=30))
        .sign(key, hashes.SHA256()))
signed = (pkcs7.PKCS7SignatureBuilder()
          .set_data(b'Urbania sample signed document. Upload testing only; not an official signature.\r\n')
          .add_signer(cert, key, hashes.SHA256())
          .sign(serialization.Encoding.DER, [pkcs7.PKCS7Options.Binary]))
(out / 'sample-signed.p7m').write_bytes(signed)
print('Created sample-outlook.msg and sample-signed.p7m. No upload or validation tests run.')
