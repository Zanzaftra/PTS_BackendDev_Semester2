<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index():view
    {$admin = \App\Models\admin::all();
    return view('admin.index', compact('admin'));

    }
    public function create():view
    {
        return view('admin.create');
    }
    public function store (Request $request)
    {
        $validated = $request->validate([
            'nama_admin' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:admins',
            'password' => 'required|string|min:6',
            'email' => 'required|email|max:100|unique:admins',
            'no_hp' => 'required|string|max:15',
            'role' => 'required|string',
            'status_admin' => 'required|string',
        ]);

        \App\Models\admin::create($request ->All());

        return redirect()->route('admin.index')->with('success', 'Admin berhasil ditambahkan.');
    }
    function edit(string $id):view
    {
        $admin = \App\Models\admin::findOrFail($id);
        return view('admin.edit', compact('admin'));
    }
    function update (Request $request,string $id)
    {
        $validated = $request->validate([
            'nama_admin' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:admins,username,'.$id,
            'email' => 'required|email|max:100|unique:admins,email,'.$id,
            'no_hp' => 'required|string|max:15',
            'role' => 'required|string',
            'status_admin' => 'required|string',
        ]);

        $admin = \App\Models\admin::findOrFail($id);
        $admin->update($request ->All());

        return redirect()->route('admin.index')->with('success', 'data berhasil diubah.');
    }
    public function destroy(string $id)
    {
        $admin = \App\Models\admin::findOrFail($id); 
        $admin->delete();
        return redirect()->route('admin.index')->with('success', 'data berhasil dihapus.');
    }
}
