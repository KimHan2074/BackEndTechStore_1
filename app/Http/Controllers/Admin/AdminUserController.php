<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class AdminUserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function getAllUsers(): JsonResponse
    {
        $users = $this->userService->getAllUsers();

        return response()->json([
            'status' => true,
            'data' => $users
        ]);
    }

    public function updateUser(Request $request, $id)
{
    $data = $request->validate([
        'name' => 'required|string|max:50',
        'email' => 'required|email|max:255',
        'password' => 'nullable|string|min:6',
        'role' => 'nullable|string|max:50',
        'address' => 'nullable|string|max:255',
        'phone' => 'nullable|string|max:20',
    ]);

    try {
        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }

        $user = $this->userService->updateUserProfile($id, $data);

        return response()->json([
            'status' => true,
            'message' => 'User profile updated successfully',
            'data' => $user
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}


}
