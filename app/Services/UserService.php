<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;

class UserService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function findById($id)
    {
        return $this->userRepository->findById($id);
    }

    public function updateUserProfile($id, $data)
    {
        return $this->userRepository->update($id, $data);
    }

    public function changePassword($id, $data)
    {
        $user = $this->userRepository->findById($id);

        if (!$user) {
            throw new \Exception("User not found with ID $id");
        }

        if (!Hash::check($data['current_password'], $user->password)) {
            throw new \Exception('Current password is incorrect');
        }

        $this->userRepository->updatePassword($id, $data['new_password']);
    }

    public function updateAvatar($userId, $avatarUrl)
    {
        return $this->userRepository->updateAvatar($userId, $avatarUrl);
    }
    public function getAllUsers()
    {
        return $this->userRepository->getAllUsers();
    }

     public function updateUser($id, $data)
    {
        return $this->userRepository->update($id, $data);
    }
}
