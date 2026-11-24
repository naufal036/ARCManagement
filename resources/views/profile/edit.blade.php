<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-[#0F1B2D] leading-tight">
            {{ __('Profil Pengguna') }}
        </h2>
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-6">

        @if (session('status') === 'profile-updated' || session('status') === 'password-updated')
            <div class="bg-emerald-50 border border-emerald-300 text-emerald-800 px-4 py-3 rounded-xl text-sm font-medium">
                Perubahan profil berhasil disimpan!
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded-xl">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white p-6 rounded-xl shadow-sm border border-[#E3E8EF]">
            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                @csrf
                @method('patch')

                <h3 class="text-lg font-bold text-[#0F1B2D] mb-6">Data Pribadi</h3>

                <div class="flex flex-col sm:flex-row items-start gap-6 pb-6 border-b border-[#E3E8EF]">
                    <div class="relative w-28 h-36 rounded-xl overflow-hidden border border-gray-300 bg-slate-100 flex items-center justify-center shrink-0">
                        @if ($user->avatar)
                            <img id="avatarPreview" src="{{ asset('storage/' . $user->avatar) }}" alt="Foto Profil"
                                class="w-full h-full object-cover">
                        @else
                            <div id="avatarInitials" class="text-3xl font-bold text-[#0A2342]">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </div>
                            <img id="avatarPreview" src="" alt="Preview" class="hidden w-full h-full object-cover">
                        @endif

                        <label for="avatarInput"
                            class="absolute bottom-0 inset-x-0 bg-[#0A2342]/80 hover:bg-[#0A2342] text-white text-xs font-medium py-1.5 text-center cursor-pointer transition">
                            Edit Foto
                        </label>
                        <input type="file" name="avatar" id="avatarInput" accept="image/*" class="hidden"
                            onchange="previewAvatar(event)">
                    </div>

                    <div class="space-y-1.5">
                        <h4 class="text-lg font-bold text-[#0F1B2D] uppercase">{{ $user->name }}</h4>
                        <p class="text-sm text-gray-600">Role: <span class="font-semibold text-[#0F1B2D]">{{ $user->role->name ?? 'Pengguna' }}</span></p>
                        <p class="text-sm text-gray-600">Email: <span class="text-gray-800">{{ $user->email }}</span></p>
                        <p class="text-sm text-gray-600">Telepon: <span class="text-gray-800">{{ $user->phone ?? '-' }}</span></p>
                        <div class="pt-1">
                            <span class="inline-block px-3 py-0.5 text-xs font-medium rounded-full border {{ $user->status === 'active' ? 'border-emerald-500 text-emerald-700 bg-emerald-50' : 'border-red-500 text-red-700 bg-red-50' }}">
                                {{ $user->status === 'active' ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6 pb-8 border-b border-[#E3E8EF]">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-[#14B8A6] focus:ring focus:ring-[#14B8A6]/20" required>
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" placeholder="Contoh: 081234567890"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-[#14B8A6] focus:ring focus:ring-[#14B8A6]/20">
                    </div>

                    <div class="md:col-span-2">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Alamat Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-[#14B8A6] focus:ring focus:ring-[#14B8A6]/20" required>
                    </div>
                </div>

                <div id="update-password" class="mt-6">
                    <h3 class="text-lg font-bold text-[#0F1B2D]">Ganti Password</h3>
                    <p class="text-xs text-gray-500 mt-1 mb-4">Kosongkan kolom di bawah ini jika tidak ingin mengubah password.</p>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Password Saat Ini</label>
                            <div class="relative flex items-center">
                                <input type="password" name="current_password" id="current_password" autocomplete="off"
                                    class="w-full pr-10 border-gray-300 rounded-lg shadow-sm focus:border-[#14B8A6] focus:ring focus:ring-[#14B8A6]/20">
                                <button type="button" onclick="toggleFieldPassword('current_password')"
                                    class="absolute right-3 text-gray-500 hover:text-gray-700 focus:outline-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                            <div class="relative flex items-center">
                                <input type="password" name="password" id="password" autocomplete="new-password"
                                    class="w-full pr-10 border-gray-300 rounded-lg shadow-sm focus:border-[#14B8A6] focus:ring focus:ring-[#14B8A6]/20">
                                <button type="button" onclick="toggleFieldPassword('password')"
                                    class="absolute right-3 text-gray-500 hover:text-gray-700 focus:outline-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
                            <div class="relative flex items-center">
                                <input type="password" name="password_confirmation" id="password_confirmation" autocomplete="new-password"
                                    class="w-full pr-10 border-gray-300 rounded-lg shadow-sm focus:border-[#14B8A6] focus:ring focus:ring-[#14B8A6]/20">
                                <button type="button" onclick="toggleFieldPassword('password_confirmation')"
                                    class="absolute right-3 text-gray-500 hover:text-gray-700 focus:outline-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end mt-8 pt-4 border-t border-[#E3E8EF]">
                    <button type="submit" class="bg-[#0A2342] text-white px-6 py-2.5 rounded-lg hover:bg-[#133660] transition font-medium text-sm">
                        Simpan Perubahan
                    </button>
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

        function toggleFieldPassword(fieldId) {
            const input = document.getElementById(fieldId);
            if (input) {
                input.type = (input.type === 'password') ? 'text' : 'password';
            }
        }
    </script>
</x-app-layout>
