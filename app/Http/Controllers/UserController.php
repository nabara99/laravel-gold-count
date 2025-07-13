<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;

class UserController extends Controller
{
    public  function index()
    {
        $users = User::all();
        $roles = Role::all();
        return view('pages.user.index', compact('users', 'roles'));
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::findOrFail($request->user_id);
        if (!$user) {
            return redirect()->back()->with('error', 'User not found.');
        }

        $user->role_id = $request->role_id;
        $user->save();

        return redirect()->route('user.index')->with('success', 'User role updated successfully.');
    }
}
