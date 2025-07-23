<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRepository
{
    protected $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function findById(int $id)
    {
        return $this->model->find($id);
    }

    public function update($id, $data)
    {
        $user = $this->findById($id);
        if (!$user) {
            throw new \Exception("User not found with ID: {$id}");
        }
        $user->update($data);
        return $user;
    }

    public function updatePassword($id, $newPassword)
    {
        $user = $this->model->find($id);
        if ($user) {
            $user->password = Hash::make($newPassword);
            $user->save();
        }
    }

    public function updateAvatar($userId, $avatarUrl)
    {
        $user = User::findOrFail($userId);
        $user->avatar = $avatarUrl;
        $user->save();

        return $user->avatar; 
    }
    public function getAllUsers()
    {
        return User::select('id','name', 'email', 'role', 'address', 'phone')->get();
    }
}
