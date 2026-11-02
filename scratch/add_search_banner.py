path = r'c:\xampp\htdocs\pauls\palVarnan\resources\views\pages\products.blade.php'
with open(path, 'r', encoding='utf-8') as f:
    c = f.read()

target = '<!-- Products Grid -->\n                <div class="row g-4">'
replacement = '''@if(!empty($search))
                    <div class="mb-20 d-flex align-items-center justify-content-between p-12 px-16 bg-main-50 border border-main-200 rounded-12">
                        <span class="text-sm text-heading fw-medium">
                            Search results for: <strong class="text-main-600">"{{ $search }}"</strong> ({{ $products->total() }} {{ Str::plural('item', $products->total()) }} found)
                        </span>
                        <a href="{{ url('/products') }}" class="text-sm text-danger fw-semibold text-decoration-none d-flex align-items-center gap-4 hover-text-danger-800">
                            <i class="ph-bold ph-x"></i> Clear Search
                        </a>
                    </div>
                @endif

                <!-- Products Grid -->
                <div class="row g-4">'''

c = c.replace(target, replacement)

with open(path, 'w', encoding='utf-8') as f:
    f.write(c)

print("SUCCESS_ADDED_SEARCH_BANNER")
