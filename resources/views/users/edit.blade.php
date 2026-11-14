@extends('layouts.app')

@section('header', 'Edit Pengguna')

@section('content')
    @php
        $isSelf = Auth::id() === $user->id;
        $isFellowAdmin = !$isSelf && $user->role && $user->role->name === 'Admin';
    @endphp

    <div class="max-w-4xl mx-auto space-y-6">

        <!-- Breadcrumb -->
        <nav class="text-sm text-gray-500">
            <a href="{{ route('dashboard') }}" class="hover:text-gray-700">Dashboard</a>
            <span class="mx-2">&gt;</span>
            <a href="{{ route('users.index') }}" class="hover:text-gray-700">Manajemen Pengguna</a>
            <span class="mx-2">&gt;</span>
            <span class="text-gray-700 font-medium">Edit Pengguna</span>
        </nav>


        @if (session('status') === 'profile-updated' || session('status') === 'password-updated')
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                Perubahan berhasil disimpan!
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($isSelf)
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-800 px-4 py-3 rounded text-sm">
                Anda tidak dapat mengubah data akun Anda sendiri di sini. Silakan gunakan menu <strong>Profil</strong> di
                pojok kanan atas.
            </div>
        @elseif ($isFellowAdmin)
            <div class="bg-blue-50 border border-blue-300 text-blue-800 px-4 py-3 rounded text-sm">
                Untuk sesama akun <strong>Admin</strong>, Anda hanya diizinkan mengubah <strong>Status Akun (Aktif /
                    Nonaktif)</strong>.
            </div>
        @endif


        <!-- Kartu Data Pribadi -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-xl font-bold text-gray-800 mb-6">Data Pribadi</h3>

            <form method="POST" action="{{ route('users.update', $user->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')


                <!-- Bagian Atas: Foto dengan tombol Edit Foto & Ringkasan Identitas -->
                <div class="flex flex-col sm:flex-row items-start gap-6 pb-6 border-b border-gray-200">
                    <!-- Kotak Foto + Overlay Edit Foto -->
                    <div
                        class="relative w-28 h-36 rounded-lg overflow-hidden border border-gray-300 bg-slate-200 flex items-center justify-center shrink-0">
                        @if ($user->avatar)
                            <img id="avatarPreview" src="{{ asset('storage/' . $user->avatar) }}" alt="Foto Profil"
                                class="w-full h-full object-cover">
                        @else
                            <div id="avatarInitials" class="text-3xl font-bold text-slate-700">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </div>
                            <img id="avatarPreview" src="" alt="Preview" class="hidden w-full h-full object-cover">
                        @endif

                        @if (!$isSelf && !$isFellowAdmin)
                            <label for="avatarInput"
                                class="absolute bottom-0 inset-x-0 bg-black/65 hover:bg-black/80 text-white text-xs font-medium py-1.5 text-center cursor-pointer transition">
                                Edit Foto
                            </label>
                            <input type="file" name="avatar" id="avatarInput" accept="image/*" class="hidden"
                                onchange="previewAvatar(event)">
                        @endif

                    </div>

                    <!-- Detail Informasi -->
                    <div class="space-y-1.5">
                        <h4 class="text-lg font-bold text-gray-900 uppercase">{{ $user->name }}</h4>
                        <p class="text-sm text-gray-600">Role: <span
                                class="font-medium text-gray-800">{{ $user->role->name ?? 'Pengguna' }}</span></p>
                        <p class="text-sm text-gray-600">Email: <span class="text-gray-800">{{ $user->email }}</span></p>
                        <p class="text-sm text-gray-600">Telepon: <span
                                class="text-gray-800">{{ $user->phone ?? '-' }}</span></p>
                        <div class="pt-1">
                            <span
                                class="inline-block px-3 py-0.5 text-xs font-medium rounded-full border {{ $user->status === 'active' ? 'border-emerald-500 text-emerald-700 bg-emerald-50' : 'border-red-500 text-red-700 bg-red-50' }}">
                                {{ $user->status === 'active' ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Form Ubah Data Diri -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}"
                            {{ $isSelf || $isFellowAdmin ? 'readonly' : 'required' }}
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 {{ $isSelf || $isFellowAdmin ? 'bg-gray-100 cursor-not-allowed' : '' }}">
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}"
                            placeholder="Contoh: 081234567890" {{ $isSelf || $isFellowAdmin ? 'readonly' : '' }}
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 {{ $isSelf || $isFellowAdmin ? 'bg-gray-100 cursor-not-allowed' : '' }}">
                    </div>

                    <div class="md:col-span-2">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Alamat Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                            {{ $isSelf || $isFellowAdmin ? 'readonly' : 'required' }}
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 {{ $isSelf || $isFellowAdmin ? 'bg-gray-100 cursor-not-allowed' : '' }}">
                    </div>

                    <!-- Role Akses -->
                    <div>
                        <label for="role_id" class="block text-sm font-medium text-gray-700 mb-1">Role Akses</label>
                        @if ($isSelf || $isFellowAdmin)
                            <input type="hidden" name="role_id" value="{{ $user->role_id }}">
                            <select disabled
                                class="w-full border-gray-300 rounded-md shadow-sm bg-gray-100 cursor-not-allowed">
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}"
                                        {{ $user->role_id == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                                @endforeach
                            </select>
                        @else
                            <select name="role_id" id="role_id"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                                required>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}"
                                        {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                        {{ $role->name }}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>

                    <!-- Status Akun -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status Akun</label>
                        <select name="status" id="status" {{ $isSelf ? 'disabled' : 'required' }}
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 {{ $isSelf ? 'bg-gray-100 cursor-not-allowed' : '' }}">
                            <option value="active" {{ old('status', $user->status) === 'active' ? 'selected' : '' }}>Aktif
                            </option>
                            <option value="inactive" {{ old('status', $user->status) === 'inactive' ? 'selected' : '' }}>
                                Nonaktif</option>
                        </select>
                    </div>

                    <!-- Password Baru Opsional -->
                    @if (!$isSelf && !$isFellowAdmin)
                        <div class="md:col-span-2">
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password Baru
                                (Opsional)</label>
                            <div class="relative flex items-center">
                                <input type="password" name="password" id="password"
                                    class="w-full pr-10 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                                <button type="button" onclick="togglePassword()"
                                    class="absolute right-3 text-gray-500 hover:text-gray-700 focus:outline-none">
                                    <svg xmlns="http://w3.org" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                        stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ingin mengubah password.</p>
                        </div>
                    @endif
                </div>

                <div class="flex justify-end gap-2 mt-6">
                    <a href="{{ route('users.index') }}"
                        class="bg-gray-500 text-white px-5 py-2 rounded-md hover:bg-gray-600 transition font-medium">Batal</a>
                    @if (!$isSelf)
                        <button type="submit"
                            class="bg-blue-600 text-white px-5 py-2 rounded-md hover:bg-blue-700 transition font-medium">
                            Perbarui Pengguna
                        </button>
                    @endif
                </div>

            </form>
        </div>


    </div>

    <script>
        function previewAvatar(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('avatarPreview');
                    const initials = document.getElementById('avatarInitials');
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    if (initials) initials.classList.add('hidden');
                };
                reader.readAsDataURL(file);
            }
        }

        function togglePassword() {
            let pwdInput = document.getElementById("password");
            if (pwdInput) {
                pwdInput.type = (pwdInput.type === "password") ? "text" : "password";
            }
        }
    </script>
@endsection
