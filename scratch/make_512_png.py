import zlib
import struct

def make_png(width, height, color=(34, 197, 94)):
    # PNG signature
    png = b'\x89PNG\r\n\x1a\n'
    # IHDR chunk
    ihdr = struct.pack('>IIBBBBB', width, height, 8, 2, 0, 0, 0)
    png += struct.pack('>I', len(ihdr)) + b'IHDR' + ihdr + struct.pack('>I', zlib.crc32(b'IHDR' + ihdr))
    # IDAT chunk
    # Raw pixel data: 1 filter byte (0) + width * 3 bytes per scanline
    scanline = b'\x00' + bytes(color) * width
    raw_data = scanline * height
    compressed = zlib.compress(raw_data)
    png += struct.pack('>I', len(compressed)) + b'IDAT' + compressed + struct.pack('>I', zlib.crc32(b'IDAT' + compressed))
    # IEND chunk
    png += struct.pack('>I', 0) + b'IEND' + struct.pack('>I', zlib.crc32(b'IEND'))
    return png

with open('c:/xampp/htdocs/pauls/scratch/test_512.png', 'wb') as f:
    f.write(make_png(512, 512, (34, 197, 94)))

print("Created 512x512 PNG successfully!")
