<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manage User') }}
        </h2>
    </x-slot>

    <div class="w-[90%] mx-auto">
        <div class="card shadow-sm border rounded-3">
            <div class="table-responsive">
                <form action="{{ route('manage-user.index') }}" method="GET" class="flex gap-4 my-6">
                    <input type="text" name="search" placeholder="Search by name, username or email"
                        value="{{ request('search') }}" class="border px-3 py-2 rounded w-1/2">

                    <select name="role" class="border pl-3 pr-12 py-2 rounded">
                        <option value="">Semua Role</option>
                        <option value="teacher" {{ request('role') == 'teacher' ? 'selected' : '' }}>Teacher</option>
                        <option value="student" {{ request('role') == 'student' ? 'selected' : '' }}>Student</option>
                    </select>

                    <button type="submit" class="bg-green-400 text-white px-4 py-2 rounded">Filter</button>
                </form>
                <table
                    class="table table-bordered table-striped mb-0 flex justify-center w-full border-separate border-spacing-[1px]">
                    <thead class="bg-green-400">
                        <tr>
                            <th class="w-[50px] text-white">No</th>
                            <th class="text-white">Name</th>
                            <th class="text-white">Username</th>
                            <th class="text-white">Email</th>
                            <th class="text-white">Role</th>
                            <th style="width: 170px;" class="text-center text-white">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white text-center">
                        @forelse ($users as $index => $user)
                            <tr class="cursor-pointer">
                                <td class="text-center py-2">{{ $users->currentPage() * 10 - 10 + $index + 1 }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->username }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @php
                                        $roleColors = [
                                            'teacher' => 'bg-yellow-400 text-dark',
                                            'student' => 'bg-blue-400',
                                        ];
                                        $roles = $user->roles->pluck('name');
                                    @endphp
                                    @foreach ($roles as $role)
                                        <span
                                            class="badge {{ $roleColors[strtolower($role)] ?? 'bg-secondary' }} px-2 py-1 rounded text-white">
                                            {{ ucfirst($role) }}
                                        </span>
                                    @endforeach
                                </td>
                                <td class="flex justify-center gap-2">
                                    @if (Route::has('manage-user.update'))
                                        <a class="w-1/3 py-2 px-0" href="{{ route('manage-user.update', $user->user_id) }}">
                                            <x-bxs-edit class="w-6 h-6 mx-auto text-gray-400" />
                                        </a>
                                    @endif

                                    @if (Route::has('manage-user.destroy'))
                                        <button x-data
                                            class="w-1/3 py-2 px-0"
                                            @click="$dispatch('open-modal', 'delete_user_{{ $user->user_id }}')">
                                            <x-bxs-trash class="w-6 h-6 mx-auto text-gray-400" />
                                        </button>

                                        <x-confirm-modal id="delete_user_{{ $user->user_id }}"
                                            message="Are you sure you want to delete {{ $user->name }}?"
                                            okLabel="Delete" cancelLabel="Cancel" :url="route('manage-user.destroy', $user->user_id)" method="DELETE" />
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    No users found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="my-8">
                    {{ $users->withQueryString()->links() }}
                </div>

            </div>
        </div>
    </div>


</x-app-layout>
