import base64

# Minimal valid 1x1 red PNG
png_bytes = base64.b64decode("iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==")
with open("c:/xampp/htdocs/pauls/scratch/test_cat.png", "wb") as f:
    f.write(png_bytes)
print("Saved test_cat.png")
