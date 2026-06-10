<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {$user = \App\Models\User::all();
    return view('users.index', compact('user'));

    }
    public function create()
    {
        return view('users.create');
    }
    public function store (Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:70',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        \App\Models\User::create($validated);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }
    function edit(string $id)
    {
        $user = \App\Models\User::findOrFail($id);
        return view('users.edit', compact('user'));
    }
    function update (Request $request,string $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:70',
            'email' => 'required|string|email|max:100|unique:users,email,'.$id,
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $user = \App\Models\User::findOrFail($id);
        $user->update($validated);

        return redirect()->route('users.index')->with('success', 'data berhasil diubah.');
    }
    public function destroy(string $id)
    {
        $user = \App\Models\User::findOrFail($id); 
        $user->delete();
        return redirect()->route('users.index')->with('success', 'data berhasil dihapus.');
    }
}