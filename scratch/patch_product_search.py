path = r'c:\xampp\htdocs\pauls\palVarnan\app\Http\Controllers\ProductController.php'
with open(path, 'r', encoding='utf-8') as f:
    c = f.read()

target1 = """        if ($request->has('gift')) {
            $products->where('is_gift', $request->gift == '1' || $request->gift === 'true' ? 1 : 0);
        }"""

replacement1 = """        if ($request->has('gift')) {
            $products->where('is_gift', $request->gift == '1' || $request->gift === 'true' ? 1 : 0);
        }

        $search = trim($request->get('search', $request->get('q', '')));
        if ($search !== '') {
            $products->where(function ($q) use ($search) {
                $q->where('product_name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhereHas('category', function ($cq) use ($search) {
                      $cq->where('category_name', 'LIKE', "%{$search}%");
                  })
                  ->orWhereHas('variants', function ($vq) use ($search) {
                      $vq->where('varient', 'LIKE', "%{$search}%");
                  });
            });
        }"""

target2 = "return view('pages.products', compact('categories', 'subcategories', 'brands', 'products', 'cartProductIds', 'cartItems', 'wishlistProductIds', 'sort', 'selectedCategory', 'selectedSubcategory'));"
replacement2 = "return view('pages.products', compact('categories', 'subcategories', 'brands', 'products', 'cartProductIds', 'cartItems', 'wishlistProductIds', 'sort', 'selectedCategory', 'selectedSubcategory', 'search'));"

if target1 in c and target2 in c:
    c = c.replace(target1, replacement1, 1)
    c = c.replace(target2, replacement2, 1)
    with open(path, 'w', encoding='utf-8') as f:
        f.write(c)
    print("SUCCESS_PATCHED_PRODUCT_CONTROLLER")
else:
    print("TARGETS_NOT_FOUND")
