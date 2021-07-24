<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

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
        $user->password = bcrypt($attrs['password']);
        $user->save();


        // Return user and tokens in response
        return response([
            'user' => $user,
            'token' => $user->createToken('secret')->plainTextToken
        ], 200);
    }

    // Login Users
    public function login(Request $request)
    {
        // Validate Fields
        $attrs = $request->validate([

            'email'      => 'required|email',
            'password'      => 'required|min:8',
        ]);

        // Attempt login
        if (!Auth::attempt($attrs)) {
            return response([
                'message' => 'Invalid Credentials.'
            ], 403);
        }

        //return user & token in response
        return response([
            'user' => auth()->user(),
            'token' => auth()->user()->plainTextToken
        ], 200);
    }

    // Logout Users
    public function logout()
    {
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
}
