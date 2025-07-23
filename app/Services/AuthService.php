<?php

namespace App\Services;

use App\Repositories\AuthRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
 use Illuminate\Http\Exceptions\HttpResponseException;
use App\Mail\SendOtpMail;
use Illuminate\Support\Str;

class AuthService
{
    protected $repository;

    public function __construct(AuthRepository $repository)
    {
        $this->repository = $repository;
    }
  
    public function register($data)
    {
        $existing = $this->repository->findByEmail($data['email']); 

        if ($existing) {
            throw ValidationException::withMessages([
                'email' => ['Email already exists.'],
            ]);
        }
        
        $data['password'] = Hash::make($data['password']);
        $data['email_otp'] = rand(100000, 999999);
        $user = $this->repository->create($data); 

        $mailer = app(\App\Services\MailerService::class);
        $body = '
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <title>Your OTP</title>
            </head>
            <body>
                <p>Your OTP code is: <strong>' . $data['email_otp'] . '</strong></p>
                <p>Please enter this code to activate your account.</p>
            </body>
            </html>
        ';

        try {
            $mailer->send($user->email, 'Your OTP code', $body);

            return response()->json([
                'status' => true,
                'message' => 'Đăng ký thành công! Vui lòng kiểm tra email để lấy OTP.'
            ]);
        } catch(\Exception $e) {
            Log::error('Send mail failed: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Đăng ký thất bại! Không thể gửi email OTP.',
                'error' => $e->getMessage()
            ], 500);
        }
        return $user;
    }
  
    public function verifyOtp($email, $email_otp)
    {
        $user = $this->repository->findByEmail($email);

        if ($user && $user->email_otp == $email_otp) {
            $user->email_verified = true;
            $user->email_otp = null;

            if ($user->email_verified && str_ends_with($user->email, '@ITDragonsTeam.com')) {
                $user->role = 'admin';
            } else {
                $user->role = 'user';
            }
            $user->save();

            $mailer = app(\App\Services\MailerService::class);
            $body = '
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset="UTF-8">
                    <title>Registration Successful</title>
                </head>
                <body>
                     <h3>Hello ' . $user->name . ',</h3>
                    <p>Your account has been successfully registered with our service.</p>
                    <p>Your login details are as follows:</p>
                    <ul>
                         <li>Full Name: ' . $user->name . '</li>
                        <li>Password: (secured, please remember it from registration)</li>
                    </ul>
                </body>
                </html>
            ';

            try {
                $mailer->send($user->email, 'Sign-up Successful!', $body);
            } catch(\Exception $e) {
                Log::error('Send mail failed: ' . $e->getMessage()); 
            }

            return true;
        }
        return false;
    }

    public function handleGoogleLogin($googleUser)
    {
        $user = $this->repository->firstOrCreateByEmail(
            $googleUser->getEmail(), 
            $googleUser->getName(), 
            Hash::make(uniqid()) // password fake
        );

        // Tạo Sanctum Token
        return $user->createToken('web')->plainTextToken;
    }

    public function login(array $credentials)
    {
        $result = $this->repository->findUserByName($credentials['name']);

        if (!$result || !$this->repository->validatePassword($result['user'], $credentials['password'])) {
            throw new HttpResponseException(response()->json([
                'message' => 'Full name or password is incorrect.'
            ], 401)); 
        }

        $token = $result['user']->createToken($result['role'] . '-token')->plainTextToken;

        return [
            'user' => $result['user'],
            'role' => $result['role'],
            'token' => $token,
        ];
    }

    public function resetPassword(array $data): void
    {
        $userData = $this->repository->findUserByName($data['name']);

        if (!$userData) {
            throw ValidationException::withMessages(['name' => 'User not found.']);
        }

        $user = $userData['user'];
        $user->password = Hash::make($data['password']);
        $user->save();
    }
}
