<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Penugasan Tim Proyek') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <form action="{{ route('project-assignment.update', $projectAssignment->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Pilih Proyek:</label>
                        <select name="project_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                            @foreach($proyeks as $p)
                                <option value="{{ $p->id }}" {{ $projectAssignment->project_id == $p->id ? 'selected' : '' }}>{{ $p->nama_proyek }}</option>
                            @endforeach
                        </select>
                        @error('project_id') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Pilih Personel / Konsultan:</label>
                        <select name="user_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}" {{ $projectAssignment->user_id == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                            @endforeach
                        </select>
                        @error('user_id') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Peran di Proyek:</label>
                        <input type="text" name="role_in_project" value="{{ old('role_in_project', $projectAssignment->role_in_project) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        @error('role_in_project') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Tanggal Mulai Ditugaskan:</label>
                        <input type="date" name="assigned_date" value="{{ old('assigned_date', $projectAssignment->assigned_date) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        @error('assigned_date') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center justify-between">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Perbarui Penugasan
                        </button>
                        <a href="{{ route('project-assignment.index') }}" class="text-gray-600 hover:underline text-sm">Kembali</a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>