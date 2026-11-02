path = r'c:\xampp\htdocs\pauls\palVarnan\app\Http\Controllers\CheckoutController.php'
with open(path, 'r', encoding='utf-8') as f:
    lines = f.readlines()

for i in range(285, 320):
    if "'user_id' => $userId," in lines[i]:
        lines[i] = "                'user_id' => $userId,\n                'country_id' => session('country_id') ?? $user->country_id ?? 243,\n"
        break

with open(path, 'w', encoding='utf-8') as f:
    f.writelines(lines)

print("SUCCESS_COUNTRY_ID_ADDED")
