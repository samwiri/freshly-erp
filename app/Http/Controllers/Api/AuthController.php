<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Customer;
use App\Models\Employee;

class AuthController extends Controller
{
    /**
     * 
     * @param Request $request
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|in:customer,employee,admin,manager',
            'phone' => 'required|string'
            // 'address' => 'nullable|string',
            // Employee-specific validation rules
            // 'position' => 'required_if:role,employee|string|max:255',
            // 'department' => 'required_if:role,employee|string|in:operations,management,customer_service,maintenance,delivery',
            // 'salary' => 'nullable|numeric|min:0',
            // 'hourly_rate' => 'nullable|numeric|min:0',
            // 'work_schedule' => 'nullable|array',
            // 'permissions' => 'nullable|array',
            // 'performance_rating' => 'nullable|integer|min:1|max:5',
            // 'last_review_date' => 'nullable|date',
            // 'employment_status' => 'nullable|string|in:active,inactive,on_leave,terminated',
            // 'emergency_contact_name' => 'nullable|string|max:255',
            // 'emergency_contact_phone' => 'nullable|string|max:20',
            // 'notes' => 'nullable|string',
        ]);

        //check is the user already exists

        if (User::where('email', $validated['email'])->exists()) {
            return response()->json([
                'message' => 'User already exists',
                'error' => 'User already exists',
            ], 409);
        }

        try {

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => bcrypt($validated['password']),
                'role' => $validated['role'],
                'phone' => $validated['phone'] ?? null,
                // 'address' => $validated['address'] ?? null,
                'status' => 'active',
            ]);
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'message' => 'User registered successfully',
                'user' => $user,
                'token' => $token,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error registering user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);
        $user = User::where('email', $validated['email'])->first();
        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }

        //check if the user is active
        if ($user->status != 'active') {
            return response()->json([
                'message' => 'User is not active',
            ], 401);
        }
        //update the last login at and last login ip
        $user->last_login_at = now();
        $user->last_login_ip = $request->ip();
        $user->save();

        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'message' => 'Login successful',
            'user' => $user->load(['customer']),
            'token' => $token,
        ], 200);
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'User not authenticated',
            ], 401);
        }

        // Delete the current access token
        $user->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ], 200);
    }
    public function checkIfEmailIsInUse(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);
        $user = User::where('email', $validated['email'])->exists();
        return response()->json([
            'message' => 'Email is available',
            'is_in_use' => $user,
        ], 200);
    }
}
