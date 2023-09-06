<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
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

    public function getUser(User $user) {
        if (auth()->user()->id !== $user->id) {
            return redirect('/');
        }

        return view('user-cab', ['user' => $user]);
    }

    public function updateUser(Request $request, User $user) {
        if (auth()->user()->id !== $user->id) {
            return redirect('/user-cab/' . $user->id);
        }

        //dd($request->all());

        if ($request->name !== $user->name) {
            $inputFields['name'] = $request->validate(['name' => ['required', 'min:3', 'max:20', 'unique:users']]);
            $inputFields['name'] = strip_tags($request->input('name'));
        } else {
            $inputFields['name'] = $user->name;
        }

        if ($request->email !== $user->email) {
            $inputFields['email'] = $request->validate(['email' => ['required', 'email', 'unique:users']]);
            $inputFields['email'] = strip_tags($request->input('email'));
        } else {
            $inputFields['email'] = $user->email;
        }

        if ($request->password !== null) {
            $inputFields['password'] = $request->validate(['password' => ['min:6', 'max:50']]);
            $inputFields['password'] = strip_tags($request->input('password'));
        }

//        $inputFields = $request->validate([
//            'name' => ['required', 'min:3', 'max:20', 'unique:users'],
//            'email' => ['required', 'email', 'unique:users'],
//            'password' => ['min:6', 'max:50'],
//            'img' => 'image'
//        ]);

        if ($request->hasFile('img')) {
            if ($user->image != 'user-img/user-default.png') {
                $deletePath = storage_path('app/public/' . $user->image);
                File::delete($deletePath);
            }

            $uploadedFile = $request->file('img');
            $filename = $uploadedFile->store('public/user-img');
            $filename = str_replace('public/', '', $filename);
            $inputFields['image'] = $filename;
            $user->update(['image' => $inputFields['image']]);
        }

        $user->update($inputFields);

        return redirect('/user-cab/' . $user->id);
    }
}
