<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('role')->get();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        if ($request->filled('email')) {
            $request->merge([
                'email' => strtolower(trim($request->email)),
            ]);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8',
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|in:active,inactive',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'status' => $request->status,
            'email_verified_at' => now(),
        ]);


        return redirect()->route('users.index')->with('success', 'Pengguna berhasil ditambahkan!');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        // 1. Admin tidak boleh mengubah datanya sendiri dari menu Manajemen Pengguna
        if (Auth::id() === $user->id) {
            return back()->withErrors('Anda tidak dapat mengubah data Anda sendiri dari halaman ini.')->withInput();
        }

        if ($request->filled('email')) {
            $request->merge([
                'email' => strtolower(trim($request->email)),
            ]);
        }


        // 2. Sesama Admin hanya boleh diubah statusnya (aktif/nonaktif)
        if ($user->role && $user->role->name === 'Admin') {
            $request->validate([
                'status' => 'required|in:active,inactive',
            ]);

            if (
                $request->name != $user->name ||
                $request->email != $user->email ||
                $request->phone != $user->phone ||
                $request->role_id != $user->role_id ||
                $request->filled('password') ||
                $request->hasFile('avatar')
            ) {

                return back()->withErrors('Anda hanya diizinkan mengubah Status (Aktif/Nonaktif) untuk sesama Admin.')->withInput();
            }

            $user->update([
                'status' => $request->status,
            ]);

            return redirect()->route('users.index')->with('success', 'Status Admin berhasil diperbarui!');
        }

        // 3. Role di bawah Admin (Manajer, Karyawan) bisa diubah seluruh datanya
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|in:active,inactive',
            'password' => 'nullable|string|min:8',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role_id' => $request->role_id,
            'status' => $request->status,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($data);



        return redirect()->route('users.index')->with('success', 'Data pengguna berhasil diperbarui!');
    }
}
