<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Verify;

class AuthController extends Controller
{
    /**
     * User Authentication
     * 
     * @param Request $request
     * @return Response
     */
    public function login(Request $request)
    {

        $input = $request->all();
        
        $validate = Validator::make($input, [
            'email' => 'required',
            'password' => 'required',
        ]);

        if ($validate->fails()) {
            return response([
                'message' => $validate->errors()->first(),
            ], 400);
        }

        $user = User::where('email', $input['email'])->first();

        if (!$user->hasVerifiedEmail()) {
            return response([
                'message' => 'Please verify your email'
            ], 403);
        }

        if ($user && $user->deleted == 1) {
            return response([
                'message' => "Account does not exist.",
                'error' => true
            ], 404);
        }

        if (!$user || !Hash::check($input['password'], $user->password)) {
            return response([
                'message' => "Your email or password is incorrect. Please try again."
            ], 401);
        }

        $token = $user->createToken('todo')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token,
        ];

        return response([
            'data' => $response,
            'message' => 'Success!',
            'success' => true
        ], 200);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();

        return response([
            'message' => "User logout."
        ]);
    }

    public function register(Request $request)
    {
        $input = $request->all();

        $validate = Validator::make($input, [
            'firstname' => 'required',
            'lastname' => 'required',
            'email' => 'required',
            'password' => 'required'
        ]);

        if ($validate->fails()) {
            return response([
                'message' => $validate->errors()->first(),
            ], 400);
        }

        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);

        // Send verification email
        event(new Registered($user));
        Mail::to($user->email);

        return response([
            'message' => 'User registered.',
            'data' => [
                'user' => $user
            ]
        ], 200);
    }
}
