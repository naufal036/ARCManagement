@extends('layouts.app')

@section('header', 'Pengaturan Sistem')

@section('content')
    <div class="bg-white p-6 rounded shadow max-w-2xl mx-auto">

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <form action="{{ route('settings.update') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label for="app_name" class="block text-gray-700 font-medium mb-2">Nama Aplikasi</label>
                <input type="text" name="app_name" id="app_name" value="{{ $appName->config_value }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" required>
                <p class="text-xs text-gray-500 mt-1">Nama ini akan muncul di pojok kiri atas aplikasi (Sidebar).</p>
            </div>

            <div class="flex justify-end gap-2 mt-6">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Simpan Pengaturan</button>
            </div>
        </form>
    </div>
@endsection
