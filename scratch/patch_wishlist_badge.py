# Patch products.blade.php
path1 = r'c:\xampp\htdocs\pauls\palVarnan\resources\views\pages\products.blade.php'
with open(path1, 'r', encoding='utf-8') as f:
    c1 = f.read()

target1 = """        if (data.status === 'added') {
            icon.classList.remove('ph-heart', 'text-neutral-400');
            icon.classList.add('ph-fill', 'ph-heart', 'text-danger-600');
        } else {
            icon.classList.remove('ph-fill', 'text-danger-600');
            icon.classList.add('ph-heart', 'text-neutral-400');
        }"""

replacement1 = """        if (data.status === 'added') {
            icon.classList.remove('ph-heart', 'text-neutral-400');
            icon.classList.add('ph-fill', 'ph-heart', 'text-danger-600');
        } else {
            icon.classList.remove('ph-fill', 'text-danger-600');
            icon.classList.add('ph-heart', 'text-neutral-400');
        }

        const badge = document.getElementById('wishlist-count-badge');
        if (badge && typeof data.count !== 'undefined') {
            badge.textContent = data.count;
        }"""

if target1 in c1:
    c1 = c1.replace(target1, replacement1, 1)
    with open(path1, 'w', encoding='utf-8') as f:
        f.write(c1)
    print("PATCHED_PRODUCTS_BLADE")
else:
    print("TARGET1_NOT_FOUND")

# Patch product-detail.blade.php
path2 = r'c:\xampp\htdocs\pauls\palVarnan\resources\views\pages\product-detail.blade.php'
with open(path2, 'r', encoding='utf-8') as f:
    c2 = f.read()

target2 = """        if (data.status === 'added') {
            icon.classList.remove('ph');
            icon.classList.add('ph-fill', 'text-danger-600');
        } else {
            icon.classList.remove('ph-fill', 'text-danger-600');
            icon.classList.add('ph');
        }"""

replacement2 = """        if (data.status === 'added') {
            icon.classList.remove('ph');
            icon.classList.add('ph-fill', 'text-danger-600');
        } else {
            icon.classList.remove('ph-fill', 'text-danger-600');
            icon.classList.add('ph');
        }

        const badge = document.getElementById('wishlist-count-badge');
        if (badge && typeof data.count !== 'undefined') {
            badge.textContent = data.count;
        }"""

if target2 in c2:
    c2 = c2.replace(target2, replacement2, 1)
    with open(path2, 'w', encoding='utf-8') as f:
        f.write(c2)
    print("PATCHED_PRODUCT_DETAIL_BLADE")
else:
    print("TARGET2_NOT_FOUND")
