<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    //


    public function register(Request $request){
        $attrs = $request->validate([
            'name'=>'required|string',
            'email'=>'required|email|unique:users,email',
            'password'=>'required|min:6|confirmed'
        ]);

        $user = User::create([
            'email'=> $attrs['email'],
            'name'=> $attrs['email'],
            'password'=> bcrypt($attrs['password'])
        ]);

        return response([
            'user'=> $user,
            'token'=> $user->createToken('secret')->plainTextToken
        ]);
    }



    public function login(Request $request){
 $attrs = $request->validate([
            'email'=>'required|email',
            'password'=>'required|min:6'
        ]);

       if(!Auth::attempt($attrs)){
            return response(
                ['message'=> 'Invalid credentials']
            );
       }

       $user = User::where('email', $request->email)->first();
        return response([
            'user'=> $user,
            'token'=> $user->createToken('secret')->plainTextToken
        ], 200);
    }

    public function logout(){
       $user = User::where('email',auth()->user()->email)->first();

        $user->tokens()->delete();
        auth()->logout();
        return response(
            [
                'message' => 'Logout success'
            ],
            200
        );
    }


    public function user(){
        return response([
            'user'=> auth()->user()
        ], 200);
    }
}
