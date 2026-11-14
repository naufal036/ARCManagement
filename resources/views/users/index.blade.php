@extends('layouts.app')

@section('header', 'Manajemen Pengguna')

@section('content')
    <div class="bg-white p-6 rounded shadow">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium">Daftar Pengguna</h3>
            <a href="{{ route('users.create') }}"
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                Tambah Pengguna
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 border-b">
                        <th class="p-3">Nama</th>
                        <th class="p-3">Email</th>
                        <th class="p-3">Role</th>
                        <th class="p-3">Status</th>
                        <th class="p-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="p-3">{{ $user->name }}</td>
                            <td class="p-3">{{ $user->email }}</td>
                            <td class="p-3">{{ $user->role ? $user->role->name : 'Tidak Ada' }}</td>
                            <td class="p-3">
                                <span
                                    class="px-2 py-1 text-xs rounded {{ $user->status == 'active' ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800' }}">
                                    {{ ucfirst($user->status) }}
                                </span>
                            </td>

                            <td class="p-3">
                                <a href="{{ route('users.edit', $user->id) }}"
                                    class="text-blue-600 hover:underline">Edit</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
