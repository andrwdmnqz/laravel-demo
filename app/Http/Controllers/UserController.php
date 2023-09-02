<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function createUser(Request $request) {
        $inputFields = $request->validate([
            'name' => ['required', 'min:3', 'max:20', 'unique:users'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'min:6', 'max:50']
        ]);

        $inputFields['password'] = Hash::make($inputFields['password']);
        $user = User::create($inputFields);

        auth()->login($user);

        return response()->json([
            'text' => 'text'
        ]);
    }

    public function logout() {

        $user = Auth::user();
        $user->update([
            'last_seen' => now(),
        ]);

        auth()->logout();
        return response()->json([
            'status' => 201
        ]);
    }

    public function login(Request $request) {
        $inputFields = $request->validate([
            'logemail' => 'required',
            'logpassword' => 'required'
        ]);

        if (auth()->attempt(['email' => $inputFields['logemail'], 'password' => $inputFields['logpassword']])) {
            $request->session()->regenerate();
        }

        return redirect('/');
    }

    public function showStatus() {
        $users = User::all();

        return response()->json($users);
    }
}
