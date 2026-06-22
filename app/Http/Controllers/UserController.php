<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('name')->get();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:bja_users,username',
            'password' => 'required|string|min:6',
            'role'     => 'required|in:admin,sales',
            'color'    => 'nullable|string|max:20',
        ]);

        $data['password'] = bcrypt($data['password']);

        User::create($data);

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function show(User $user)
    {
        return redirect()->route('users.edit', $user);
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:bja_users,username,' . $user->id,
            'password' => 'nullable|string|min:6',
            'role'     => 'required|in:admin,sales',
            'color'    => 'nullable|string|max:20',
        ]);

        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = bcrypt($data['password']);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'User berhasil diupdate.');
    }

    public function destroy(User $user)
    {
        $authId = session('auth_user.id');

        if ($user->id === 1) {
            return redirect()->back()->with('error', 'Admin utama tidak bisa dihapus.');
        }

        if ($user->id === $authId) {
            return redirect()->back()->with('error', 'Anda tidak bisa menghapus akun sendiri.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }
}
