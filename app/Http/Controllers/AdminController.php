<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {$admin = \App\Models\admin::all();
    return view('admin.index', compact('admin'));

    }
    public function create()
    {
        return view('admin.create');
    }
    public function store (Request $request)
    {
        $validated = $request->validate([
            'nama_admin' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:admin',
            'password' => 'required|string|min:6',
            'email' => 'required|email|max:100|unique:admin',
            'no_hp' => 'required|string|max:15',
            'role' => 'required|string',
            'status_admin' => 'required|string',
        ]);

        \App\Models\admin::create($validated);

        return redirect()->route('admin.index')->with('success', 'Admin berhasil ditambahkan.');
    }
    function edit(string $id)
    {
        $admin = \App\Models\admin::findOrFail($id);
        return view('admin.edit', compact('admin'));
    }
    function update (Request $request,string $id)
    {
        $validated = $request->validate([
            'nama_admin' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:admin,username,'.$id.',id_admin',
            'email' => 'required|email|max:100|unique:admin,email,'.$id.',id_admin',
            'no_hp' => 'required|string|max:15',
            'role' => 'required|string',
            'status_admin' => 'required|string',
        ]);

        $admin = \App\Models\admin::findOrFail($id);
        $admin->update($validated);

        return redirect()->route('admin.index')->with('success', 'data berhasil diubah.');
    }
    public function destroy(string $id)
    {
        $admin = \App\Models\admin::findOrFail($id); 
        $admin->delete();
        return redirect()->route('admin.index')->with('success', 'data berhasil dihapus.');
    }
}
