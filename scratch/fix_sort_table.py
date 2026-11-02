path = r'c:\xampp\htdocs\pauls\palVarnan\app\Http\Controllers\ProductController.php'
with open(path, 'r', encoding='utf-8') as f:
    c = f.read()

c = c.replace(
    "$products->orderByRaw('(SELECT MIN(offer_price) FROM product_varients WHERE product_varients.product_id = products.id) ASC');",
    "$products->orderByRaw('(SELECT MIN(offer_price) FROM product_varient WHERE product_varient.product_id = products.id) ASC');"
)

c = c.replace(
    "$products->orderByRaw('(SELECT MAX(offer_price) FROM product_varients WHERE product_varients.product_id = products.id) DESC');",
    "$products->orderByRaw('(SELECT MAX(offer_price) FROM product_varient WHERE product_varient.product_id = products.id) DESC');"
)

with open(path, 'w', encoding='utf-8') as f:
    f.write(c)

print('FIXED_PRODUCT_SORT_TABLE_NAME')
