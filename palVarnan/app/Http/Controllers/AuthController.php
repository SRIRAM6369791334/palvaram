<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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

       
        $countryId = session('country_id');

 

        User::create([

            'country_id'   => $countryId,

            'name'         => $request->name,
            'email'        => $request->email,
            'phone_number' => $request->phone_number,

            'last_name'    => $request->last_name,

            'password'     => Hash::make($request->password),
        ]);

        return redirect('/login')
            ->with('success', 'Registered Successfully');
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

        return response()->json([
            'success' => true,
            'message' => 'Login successful'
        ]);
    }


    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'success' => true,
                'message' => __($status)
            ]);
        }

        return response()->json([
            'errors' => [
                'email' => [__($status)]
            ]
        ], 422);
    }


    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'errors' => [
                    'email' => ['User not found']
                ]
            ], 422);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        Auth::logout();

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully'
        ]);
    }


}
