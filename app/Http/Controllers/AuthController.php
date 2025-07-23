<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    protected $service;

    public function __construct(AuthService $service)
    {
        $this->service = $service;
    }
  
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        try {
            $this->service->register($validated);
            return response()->json(['message' => 'Signup successful. Check your email for OTP.'], 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }
  
    public function verify(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'email_otp' => 'required|integer',
        ]);

        $input = $validated;

        $verified = $this->service->verifyOtp($request->input('email'), $request->input('email_otp'));

        if ($verified) {
            return response()->json(['message' => 'Email verified and role assigned successfully.']); 
        }
        return response()->json(['message' => 'Invalid OTP.'], 400);
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }
  
    public function handleGoogleCallback()
    {
        $googleUser = Socialite::driver('google')->user();

        $token = $this->service->handleGoogleLogin($googleUser);
        
        return redirect('https://frontendreacttechstore-production.up.railway.app/callback?token=' . $token);
    }

    public function login(Request $request){
        $data = $request->validate([
            'name' => 'required|string',
            'password' => 'required|string|min:6',
        ]);

        $result = $this->service->login($data);

        return response()->json([
            'user' => $result['user'],
            'role' => $result['role'],
            'token' =>$result['token'],
        ]);
    }

    public function resetPassword(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $this->service->resetPassword($data);

        return response()->json([
            'message' => 'Password has been reset successfully.'
        ]);
    }

    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully.']);
    }
}

