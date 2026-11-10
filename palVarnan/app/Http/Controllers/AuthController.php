<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Cart;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    public function registerStore(Request $request)
    {
        $request->validate([
            'last_name'    => 'required',
            'name'         => 'required',
            'email'        => 'required|email|unique:users,email',
            'phone_number' => 'required',
            'password'     => 'required|min:6',
        ]);

        try {
            $countryId = session('country_id') ?? 243;
            $currentYear = date('y');
            $lastUserId = User::max('id') ?? 0;
            $newUserId = sprintf('HG-%s-%05d', $currentYear, $lastUserId + 1);

            $fullName = trim($request->name . ' ' . $request->last_name);

            $user = User::create([
                'user_id'      => $newUserId,
                'country_id'   => $countryId,
                'name'         => $fullName,
                'first_name'   => $request->name,
                'last_name'    => $request->last_name,
                'email'        => strtolower(trim($request->email)),
                'phone_number' => $request->phone_number,
                'password'     => Hash::make($request->password),
            ]);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success'      => true,
                    'message'      => 'Account created successfully! You can now log in.',
                    'redirect_url' => route('login')
                ]);
            }

            return redirect('/login')->with('success', 'Registered Successfully');
        } catch (\Exception $e) {
            Log::error('Registration failed: ' . $e->getMessage());

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Registration failed: ' . $e->getMessage()
                ], 500);
            }

            return back()->withInput()->with('error', 'Registration failed: ' . $e->getMessage());
        }
    }

  
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);


        $user = User::where('email', $request->username)
                    ->orWhere('phone_number', $request->username)
                    ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'errors' => [
                    'username' => ['Invalid login credentials']
                ]
            ], 422);
        }

        Auth::login($user);

        // Migrate Guest Cart to Logged-in User
        try {
            $guestItems = Cart::where('ip_addresscart', $request->ip())
                ->whereNull('user_id')
                ->get();

            foreach ($guestItems as $item) {
                $userExisting = Cart::where('user_id', $user->id)
                    ->where('product_id', $item->product_id)
                    ->where('product_varient_id', $item->product_varient_id)
                    ->first();

                if ($userExisting) {
                    $userExisting->product_quantity += $item->product_quantity;
                    $userExisting->save();
                    $item->delete();
                } else {
                    $item->user_id = $user->id;
                    $item->save();
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Guest cart migration failed: ' . $e->getMessage());
        }

        $redirectUrl = $request->input('redirect', '/');

        return response()->json([
            'success'      => true,
            'message'      => 'Login successful',
            'redirect_url' => $redirectUrl
        ]);
    }


    public function sendResetOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ], [
            'email.exists' => 'No account found with this email address.'
        ]);

        $email = strtolower(trim($request->email));
        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json([
                'errors' => ['email' => ['No account found with this email address.']]
            ], 422);
        }

        $otp = (string) random_int(100000, 999999);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token' => Hash::make($otp),
                'created_at' => now()
            ]
        );

        try {
            Mail::send([], [], function ($message) use ($email, $otp, $user) {
                $userName = htmlspecialchars($user->name ?? 'Customer');
                $message->to($email)
                    ->subject('Password Reset OTP - Pal Varnan')
                    ->html("
                        <div style='font-family: Arial, sans-serif; max-width: 500px; margin: 0 auto; padding: 24px; border: 1px solid #e5e7eb; border-radius: 12px; background-color: #ffffff;'>
                            <div style='text-align: center; margin-bottom: 20px;'>
                                <h2 style='color: #1c799b; margin: 0;'>Pal Varnan</h2>
                            </div>
                            <p style='font-size: 15px; color: #374151;'>Hello <strong>{$userName}</strong>,</p>
                            <p style='font-size: 14px; color: #4b5563; line-height: 1.5;'>We received a request to reset your password. Use the verification code below to set a new password:</p>
                            <div style='background-color: #f0fdfa; border: 1px dashed #0d9488; border-radius: 8px; padding: 16px; text-align: center; font-size: 32px; font-weight: 700; letter-spacing: 6px; color: #0f766e; margin: 24px 0;'>
                                {$otp}
                            </div>
                            <p style='color: #6b7280; font-size: 13px; line-height: 1.4;'>This OTP is valid for <strong>15 minutes</strong>. If you did not make this request, your account remains secure and you can safely ignore this email.</p>
                            <hr style='border: none; border-top: 1px solid #f3f4f6; margin: 20px 0;'>
                            <p style='color: #9ca3af; font-size: 12px; text-align: center; margin: 0;'>&copy; " . date('Y') . " Pal Varnan. All rights reserved.</p>
                        </div>
                    ");
            });

            return response()->json([
                'success' => true,
                'message' => 'Verification code sent to your registered email.'
            ]);
        } catch (\Throwable $e) {
            Log::error('Password reset email sending failed: ' . $e->getMessage());
            return response()->json([
                'errors' => ['email' => ['Could not send verification email. Please try again.']]
            ], 500);
        }
    }

    public function resetPasswordWithOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|digits:6',
            'password' => 'required|min:6',
        ], [
            'otp.digits' => 'Verification code must be exactly 6 digits.',
            'password.min' => 'Password must be at least 6 characters.'
        ]);

        $email = strtolower(trim($request->email));
        $record = DB::table('password_reset_tokens')->where('email', $email)->first();

        if (!$record) {
            return response()->json([
                'errors' => ['otp' => ['No verification code requested or it has already been used. Please request a new code.']]
            ], 422);
        }

        if (Carbon::parse($record->created_at)->addMinutes(15)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();
            return response()->json([
                'errors' => ['otp' => ['Verification code has expired. Please request a new one.']]
            ], 422);
        }

        if (!Hash::check($request->otp, $record->token)) {
            return response()->json([
                'errors' => ['otp' => ['Incorrect verification code. Please check your email and try again.']]
            ], 422);
        }

        $user = User::where('email', $email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        DB::table('password_reset_tokens')->where('email', $email)->delete();
        Auth::logout();

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully! You can now log in.'
        ]);
    }


}
