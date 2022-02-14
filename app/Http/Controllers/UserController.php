<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


class UserController extends Controller
{
    public function login()
    {
        return view('user.login');
    }

    public function register()
    {
        return view('user.register');
    }

    public function get(Request $request)
    {
        // dd($request);
        $user = User::where(['email' => $request->email])->firstOrFail();
        return response()->json($user, 200);
    }

    public function signIn(Request $request)
    {
        $user = User::where(['email' => $request->email])->firstOrFail();
        Auth::login($user);
        return response()->json([], 200);

        // redirect(url('home'));
    }

    public function signUp(Request $request)
    {
        $file = $request->file('image');
        $imageName = Str::random() . '.jpg';
        $file->move('images', $imageName);
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'image_url' => $imageName,
            'password' => Hash::make('password',)
        ]);
        return response()->json([], 201);
    }
}
