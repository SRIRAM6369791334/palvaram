path = r'c:\xampp\htdocs\pauls\palVarnan\app\Http\Controllers\CheckoutController.php'
with open(path, 'r', encoding='utf-8') as f:
    lines = f.readlines()

new_block = '''    private function validateAddress(Request $request): array
    {
        $countryId = Auth::user()->country_id ?? session('country_id', 243);
        $isIndia = in_array((int)$countryId, [243, 99, 101]);

        $phoneRule = $isIndia 
            ? ['required', 'regex:/^[6-9][0-9]{9}$/']
            : ['required', 'regex:/^[0-9+ \\-]{7,20}$/'];

        $pincodeRule = $isIndia 
            ? ['required', 'regex:/^[1-9][0-9]{5}$/']
            : ['required', 'regex:/^[0-9a-zA-Z \\-]{3,10}$/'];

        return $request->validate([
            'first_name'               => ['required', 'string', 'max:100'],
            'last_name'                => ['required', 'string', 'max:100'],
            'phone_number'             => $phoneRule,
            'email'                    => ['required', 'email', 'max:255'],
            'address_line_one'         => ['required', 'string', 'max:255'],
            'address_line_two'         => ['nullable', 'string', 'max:255'],
            'landmark'                 => ['nullable', 'string', 'max:255'],
            'city'                     => ['required', 'string', 'max:100'],
            'state'                    => ['required', 'string', 'max:100'],
            'pincode'                  => $pincodeRule,
        ], [
            'phone_number.regex'       => 'Please enter a valid 10-digit mobile number starting with 6, 7, 8, or 9.',
            'pincode.regex'            => 'Please enter a valid 6-digit postal pincode.',
        ]);
    }
'''

# Verify target
assert 'private function validateAddress(Request $request): array' in lines[671]
assert 'return $request->validate([' in lines[673]

lines[671:686] = [new_block]

with open(path, 'w', encoding='utf-8') as f:
    f.writelines(lines)

print("SUCCESSFULLY_UPDATED_VALIDATE_ADDRESS")
