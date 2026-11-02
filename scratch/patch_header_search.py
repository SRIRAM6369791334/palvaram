path = r'c:\xampp\htdocs\pauls\palVarnan\resources\views\layouts\header.blade.php'
with open(path, 'r', encoding='utf-8') as f:
    c = f.read()

# Desktop form
target1 = '<form action="/search" method="GET" class="position-relative flex-grow-1 d-md-block d-none m-0 js-search">'
replacement1 = '<form action="/products" method="GET" class="position-relative flex-grow-1 d-md-block d-none m-0 js-search">'

# Desktop input
target2 = '<input type="text" id="globalSearch" name="q"'
replacement2 = '<input type="text" id="globalSearch" name="search" value="{{ request(\'search\') }}"'

# Mobile form
target3 = '<form action="/search" method="GET" class="position-relative m-0 js-search">'
replacement3 = '<form action="/products" method="GET" class="position-relative m-0 js-search">'

# Mobile input
target4 = '<input type="text" name="q"'
replacement4 = '<input type="text" name="search" value="{{ request(\'search\') }}"'

c = c.replace(target1, replacement1)
c = c.replace(target2, replacement2)
c = c.replace(target3, replacement3)
c = c.replace(target4, replacement4)

with open(path, 'w', encoding='utf-8') as f:
    f.write(c)

print("SUCCESS_PATCHED_HEADER_SEARCH")
