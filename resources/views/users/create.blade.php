@extends('layouts.app')

@section('header', 'Tambah Pengguna Baru')

@section('content')
    <div class="bg-white p-6 rounded shadow max-w-2xl mx-auto">
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('users.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label for="name" class="block text-gray-700 font-medium mb-2">Nama Lengkap</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                    required>
            </div>

            <div class="mb-4">
                <label for="email" class="block text-gray-700 font-medium mb-2">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                    required>
            </div>

            <div class="mb-4">
                <label for="phone" class="block text-gray-700 font-medium mb-2">Nomor Telepon</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                    placeholder="Contoh: 081234567890"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
            </div>

            <div class="mb-4 relative">
                <label for="password" class="block text-gray-700 font-medium mb-2">Password Dasar</label>
                <div class="relative flex items-center">
                    <input type="password" name="password" id="password"
                        class="w-full pr-10 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                        required>
                    <button type="button" onclick="togglePassword()"
                        class="absolute right-3 text-gray-500 hover:text-gray-700 focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                    </button>
                </div>
                <p class="text-xs text-gray-500 mt-1">Gunakan password standar (misal: password123) untuk login pertama kali.</p>
            </div>

            <div class="mb-4">
                <label for="role_id" class="block text-gray-700 font-medium mb-2">Role Akses</label>
                <select name="role_id" id="role_id"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                    required>
                    <option value="">-- Pilih Role --</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="status" class="block text-gray-700 font-medium mb-2">Status Akun</label>
                <select name="status" id="status"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                    required>
                    <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>

            <div class="flex justify-end gap-2 mt-6">
                <a href="{{ route('users.index') }}"
                    class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Batal</a>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Simpan</button>
            </div>
        </form>
    </div>

    <script>
        function togglePassword() {
            let pwdInput = document.getElementById("password");
            pwdInput.type = (pwdInput.type === "password") ? "text" : "password";
        }
    </script>
@endsection
