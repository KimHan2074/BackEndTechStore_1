<?php

namespace App\Repositories;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthRepository
{
    public function create($data)
    {
        return User::create($data);
    }

    public function findByEmail($email)
    {
        return User::where('email',$email)->first();
    }

    public function firstOrCreateByEmail($email, $name, $hashedPass)
    {
        return User::firstOrCreate(['email' => $email], [
            'name' => $name,
            'password' => $hashedPass
        ]);
    }

    public function findUserByName(string $name): ?array
    {
        $user = User::where('name', $name)->first();

        if ($user) {
            return ['user' => $user, 'role' => $user->role];
        }

        return null;
    }

    public function validatePassword($user, $password): bool
    {
        return Hash::check($password, $user->password);
    }
}
