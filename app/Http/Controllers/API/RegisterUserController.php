<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterUserRequest;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterUserController extends Controller
{
    use HttpResponses;

    public function register(RegisterUserRequest $request)
    {
        // Validate and sanitize the input
        $data = $request->validated();

        // Check if mobile number already exists
//        if (User::where('mobile_no', $data['mobile_no'])->exists()) {
//            return $this->error('Registration failed', [
//                'mobile_no' => 'This mobile number is already registered.',
//            ], 422);
//        }

        // Create the new user
        $user = User::create([
            'name' => $data['name'],
            'mobile_no' => $data['mobile_no'],
            'password' => Hash::make($data['password']),
        ]);

        $user->addRole('superadministrator');

        // Return the success response
        return $this->success('User registered successfully', [
            'user' => $user,
        ], 201);
    }
}
