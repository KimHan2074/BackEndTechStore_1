<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
public function getCurrentUserId()
    {
        try {
            $user = Auth::guard('user')->user();
            if (!$user) {
                return response()->json(['message' => 'User not authenticated'], 401);
            }

            $userId = $user->id;

            return response()->json(['userId' => $userId], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getUserById($id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            return response()->json(['data' => $user], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function updateProfile(Request $request, $id)
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

            return response()->json(['message' => 'User profile updated successfully', 'data' => $user], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function changePassword(Request $request, $id)
    {
        $data = $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        try {
            $result = $this->userService->changePassword($id, $data);
            return response()->json(['message' => 'Password changed successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function updateAvatar(Request $request, $id)
    {
        $request->validate([
            'avatar' => 'required|url', 
        ]);

        try {
            $result = $this->userService->updateAvatar($id, $request->avatar);
            return response()->json([
                'success' => true,
                'message' => 'Avatar updated successfully.',
                'avatar' => $result,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update avatar.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
