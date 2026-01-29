<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function register(Request $req)
    {
        $data = $req->validate([
            'name' => 'required|string|max:255',
            'email'=> 'required|email|unique:users,email',
            'password'=> 'required|min:6|confirmed',
            'mobile'=>'required'
        ]);

        $user = User::create([
            'name'=>$data['name'],
            'email'=>$data['email'],
            'mobile'=>$data['mobile'],
            'password'=> Hash::make($data['password']),
        ]);

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'user'=>$user,
            'token'=>$token
        ], 201);
    }

    public function login(Request $req)
    {
        $data = $req->validate([
            'email'=>'required|email',
            'password'=>'required'
        ]);

        $user = User::where('email', $data['email'])->first();

        if(! $user || ! Hash::check($data['password'], $user->password)){
            throw ValidationException::withMessages([
                'email'=> ['The provided credentials are incorrect.']
            ]);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'user'=>$user,
            'token'=>$token
        ]);
    }

    public function logout(Request $req)
    {
        $req->user()->currentAccessToken()->delete();

        return response()->json(['message'=>'Logged out']);
    }

    public function me(Request $req)
    {
        return response()->json($req->user());
    }
}
