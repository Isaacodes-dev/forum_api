<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterRequest;

class AuthenticationController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $isvalidated = $request->validated();
        
        if($isvalidated){
            $userData = [
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ];
            $user = User::create($userData);
            $token = $user->createToken('forumApp')->plainTextToken;
            return response([
                'user' => $user,
                'token' => $token
            ],201);
        }
        else{
            $request->validated();
        }

    }
    
    public function login(LoginRequest $loginRequest)
    {
        $isValidated = $loginRequest->validated();
        
        if($isValidated)
        {
            
            $user = User::whereusername($loginRequest->username)->first();
            if(!$user || !Hash::check($loginRequest->password, $user->password))
            {
                return response([
                    'message' => 'invalid credentials'
                ],422);
            }
            else
            {
                $token = $user->createToken('forumApp')->plainTextToken;
                return response([
                    'user' => $user,
                    'token' => $token
                ],200);
            }
        }
    }
}
