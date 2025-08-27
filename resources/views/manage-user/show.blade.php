<x-app-layout>
    <div class="w-[90%] md:w-1/2 mx-auto my-12">
        <a href="{{ url()->previous() }}" class="text-sm text-gray-600 flex items-center gap-2 mb-6 cursor-pointer">
            <x-bx-arrow-back class="w-4 h-4 text-gray-600" />
            {{ __('Back') }}
        </a>
        <form id="updateForm" method="POST" action="{{ route('manage-user.update', $user->user_id) }}">
            @csrf
            @method('PUT')

            <div>
                <x-input-label for="name" :value="__('Name')" />
                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $user->name)"
                    required autofocus autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="username" :value="__('Username')" />
                <x-text-input id="username" class="block mt-1 w-full" type="text" name="username" :value="old('username', $user->username)"
                    required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('username')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $user->email)"
                    required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="role" :value="__('Role')" />
                <select id="role" name="role"
                    class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="teacher" {{ old('role', $user->roles[0]->name) == 'teacher' ? 'selected' : '' }}>
                        Teacher
                    </option>
                    <option value="student" {{ old('role', $user->roles[0]->name) == 'student' ? 'selected' : '' }}>
                        Student
                    </option>
                </select>
                <x-input-error :messages="$errors->get('role')" class="mt-2" />
            </div>
        </form>

        @if (Route::has('manage-user.update'))
            <div class="flex items-center justify-end mt-4">
                <x-primary-button x-data @click="$dispatch('open-modal', 'save_confirmation')" class="ml-4">
                    {{ __('Save') }}
                </x-primary-button>
                <x-confirm-modal id="save_confirmation" message="Are you sure you want to save this changes?"
                    okLabel="Save" cancelLabel="Cancel" :url="route('manage-user.update', $user->user_id)" method="PUT" formId="updateForm" />
            </div>
        @endif
    </div>

</x-app-layout>
