with open('palVarnan/resources/views/layouts/header.blade.php', 'r', encoding='utf-8') as f:
    text = f.read()

target = """        searchInput.addEventListener("keyup", function () {
            clearTimeout(timeout);
            let query = this.value.trim();

            if (query.length < 2) {
                resultsBox.classList.add("d-none");
                resultsBox.innerHTML = "";
                return;
            }

            timeout = setTimeout(() => {
                fetch(`/search?q=${encodeURIComponent(query)}`)"""

replacement = """        const doSearch = function () {
            clearTimeout(timeout);
            let query = searchInput.value.trim();

            if (query.length < 2) {
                resultsBox.classList.add("d-none");
                resultsBox.innerHTML = "";
                return;
            }

            timeout = setTimeout(() => {
                fetch(`/search?q=${encodeURIComponent(query)}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })"""

target = target.replace("\r\n", "\n").replace("\n", "\r\n") if "\r\n" in text else target
replacement = replacement.replace("\r\n", "\n").replace("\n", "\r\n") if "\r\n" in text else replacement

if target in text:
    text = text.replace(target, replacement, 1)
    # Also hook up input event at the end
    end_target = "        });\n\n        document.addEventListener(\"click\", function(e) {"
    end_target = end_target.replace("\r\n", "\n").replace("\n", "\r\n") if "\r\n" in text else end_target
    end_repl = "        };\n        searchInput.addEventListener(\"input\", doSearch);\n        searchInput.addEventListener(\"keyup\", doSearch);\n\n        document.addEventListener(\"click\", function(e) {"
    end_repl = end_repl.replace("\r\n", "\n").replace("\n", "\r\n") if "\r\n" in text else end_repl
    text = text.replace(end_target, end_repl, 1)
    with open('palVarnan/resources/views/layouts/header.blade.php', 'w', encoding='utf-8') as f:
        f.write(text)
    print("Successfully replaced search handler!")
else:
    print("Target not found!")
