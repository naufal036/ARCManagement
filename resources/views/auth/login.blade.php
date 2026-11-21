<x-guest-layout>
    <!-- Header Sambutan -->
    <div class="mb-8">
        <h1 class="text-2xl sm:text-3xl font-bold text-[#0F1B2D] tracking-tight">
            Selamat Datang Kembali
        </h1>
        <p class="text-sm text-gray-500 mt-2">
            Masukkan email dan kata sandi Anda untuk mengakses akun.
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Alamat Email -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">
                Email
            </label>
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                placeholder="nama@arcmanagement.com"
                required autofocus autocomplete="username"
                class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm text-[#0F1B2D] placeholder-gray-400 focus:border-[#14B8A6] focus:ring focus:ring-[#14B8A6]/20 transition">
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">
                Password
            </label>
            <div class="relative flex items-center">
                <input id="password" type="password" name="password"
                    placeholder="Masukkan kata sandi"
                    required autocomplete="current-password"
                    class="w-full px-4 py-2.5 pr-11 rounded-xl border border-gray-300 text-sm text-[#0F1B2D] placeholder-gray-400 focus:border-[#14B8A6] focus:ring focus:ring-[#14B8A6]/20 transition">

                <button type="button" onclick="togglePassword()" aria-label="Tampilkan kata sandi"
                    class="absolute right-3.5 text-gray-400 hover:text-gray-600 focus:outline-none">
                    <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        <!-- Remember Me & Lupa Kata Sandi -->
        <div class="flex items-center justify-between text-sm">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" name="remember"
                    class="rounded border-gray-300 text-[#0A2342] shadow-sm focus:ring-[#14B8A6]">
                <span class="ms-2 text-gray-600">Ingat Saya</span>
            </label>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}"
                    class="font-medium text-[#0A2342] hover:text-[#14B8A6] transition">
                    Lupa Kata Sandi?
                </a>
            @endif
        </div>

        <!-- Tombol Masuk Penuh -->
        <div class="pt-1">
            <button type="submit"
                class="w-full py-2.5 px-4 rounded-xl bg-[#0A2342] hover:bg-[#133660] text-white font-semibold text-sm shadow-sm transition duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#0A2342]">
                Masuk
            </button>
        </div>
    </form>

    <script>
        function togglePassword() {
            const pwdInput = document.getElementById("password");
            const eyeIcon = document.getElementById("eye-icon");

            if (pwdInput.type === "password") {
                pwdInput.type = "text";
                eyeIcon.innerHTML =
                    `<path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />`;
            } else {
                pwdInput.type = "password";
                eyeIcon.innerHTML =
                    `<path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />`;
            }
        }
    </script>
</x-guest-layout>
