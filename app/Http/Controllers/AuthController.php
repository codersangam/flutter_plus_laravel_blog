<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    // Register Users
    public function register(Request $request)
    {
        $attrs = $request->validate([

            // Validate Fields
            'name'          => 'required|string',
            'email'      => 'required|email|unique:users,email',
            'password'      => 'required|min:8|confirmed',
        ]);

        // Create User
        $user = new User();
        $user->name = $attrs['name'];
        $user->email = $attrs['email'];
        $user->password = Hash::make('password');
        $user->save();

        $token = $user->createToken('secret')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 200);
    }

    // Login Users
    public function login(Request $request)
    {
        // Validate Fields
        $attrs = $request->validate([

            'email'      => 'required|email',
            'password'      => 'required|min:8',
        ]);

        $user = User::where('email', $attrs['email'])->first();

        if (!$user || Hash::check($attrs['password'], $user->password)) {
            return response([
                'message' => 'Invalid Credentials'
            ], 401);
        }

        $token = $user->createToken('secret')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }

    // Logout Users
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response([
            'message' => 'Logout success.'
        ], 200);
    }

    // User Details
    public function user()
    {
        return response([
            'user' => auth()->user()
        ], 200);
    }

    // update users
    public function update(Request $request)
    {
        $attrs = $request->validate([
            'name' => 'required|string'
        ]);

        $image = $this->saveImage($request->image, 'profiles');

        auth()->user()->update([
            'name' => $attrs['name'],
            'image' => $image
        ]);

        return response([
            'message' => 'User updated.',
            'user' => auth()->user()
        ], 200);
    }
}
