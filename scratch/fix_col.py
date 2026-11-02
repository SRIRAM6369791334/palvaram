path = r'c:\xampp\htdocs\pauls\palVarnan\app\Http\Controllers\ProductController.php'
with open(path, 'r', encoding='utf-8') as f:
    c = f.read()

c = c.replace("->orWhere('description', 'LIKE',", "->orWhere('product_description', 'LIKE',")

with open(path, 'w', encoding='utf-8') as f:
    f.write(c)

print('FIXED_PRODUCT_DESCRIPTION_COLUMN')
