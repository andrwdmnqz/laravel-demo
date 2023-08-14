<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function createUser(Request $request) {
        $inputFields = $request->validate([
            'name' => ['required', 'min:3', 'max:20', Rule::unique('users', 'name')],
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'password' => ['required', 'min:6', 'max:50']
        ]);

        $inputFields['password'] = bcrypt($inputFields['password']);
        $user = User::create($inputFields);

        auth()->login($user);

        return redirect('/');
    }

    public function logout() {
        auth()->logout();
        return redirect('/');
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
}
