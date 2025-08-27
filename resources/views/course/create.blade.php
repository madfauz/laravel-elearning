<x-app-layout>
    <div class="w-[90%] md:w-1/2 mx-auto my-12">
        <a href="{{ route('manage-course.index') }}" class="text-sm text-gray-600 flex items-center gap-2 mb-6 cursor-pointer">
            <x-bx-arrow-back class="w-4 h-4 text-gray-600" />
            {{ __('Back') }}
        </a>
        <form id="create_course" method="POST" action="{{ route('course.store') }}" enctype="multipart/form-data">
            @csrf

            <div>
                <x-input-label for="title" :value="__('Course Name')" />
                <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title')"
                    required autofocus autocomplete="title" />
                <x-input-error :messages="$errors->get('title')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="description" :value="__('description')" />
                <x-text-input id="description" class="block mt-1 w-full" type="text" name="description"
                    :value="old('description')" autofocus autocomplete="description" />
                <x-input-error :messages="$errors->get('description')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="type" :value="__('type')" />
                <select id="type" name="type"
                    class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="public" {{ old('type') == 'public' ? 'selected' : '' }}>
                        Public
                    </option>
                    <option value="private" {{ old('type') == 'private' ? 'selected' : '' }}>
                        Private
                    </option>
                </select>
                <x-input-error :messages="$errors->get('type')" class="mt-2" />
            </div>

            <div id="access_code_wrapper" class="mt-4 hidden">
                <x-input-label for="access_code" :value="__('Access Code')" />
                <x-text-input id="access_code" class="block mt-1 w-full" type="text" name="access_code"
                    :value="old('access_code')" autocomplete="access_code" />
                <x-input-error :messages="$errors->get('access_code')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="cover_path" :value="__('Thumbnail')" />
                <input id="cover_path"
                    class="block w-full px-4 py-2 mt-1 text-sm text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm file:mr-4 file:py-2 file:px-4
           file:rounded-md file:border-0 file:text-sm file:font-semibold
           file:bg-green-500 file:text-white hover:file:bg-green-700 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    type="file" name="cover_path" accept="image/*" required />

                <div id="cover_preview_wrapper" class="mt-2 hidden">
                    <p class="text-sm text-gray-500 mb-1">Preview:</p>
                    <img id="cover_preview" src="" alt="Thumbnail Preview" class="max-w-xs rounded shadow" />
                </div>

                <x-input-error :messages="$errors->get('cover_path')" class="mt-2" />
            </div>
        </form>

        @if (Route::has('course.create'))
            <div class="flex items-center justify-end mt-4">
                <x-primary-button x-data @click="$dispatch('open-modal', 'save_confirmation')" class="ml-4">
                    {{ __('Save') }}
                </x-primary-button>
                <x-confirm-modal id="save_confirmation" message="Are you sure you want to create new Course?"
                    okLabel="Save" cancelLabel="Cancel" :url="route('course.store')" formId="create_course" />
            </div>
        @endif
    </div>

    <script>
        document.getElementById('type').addEventListener('change', function() {
            const accessCodeWrapper = document.getElementById('access_code_wrapper');
            if (this.value === 'private') {
                accessCodeWrapper.classList.remove('hidden');
            } else {
                accessCodeWrapper.classList.add('hidden');
            }
        });

        document.getElementById('type').dispatchEvent(new Event('change'));


        document.getElementById('cover_path').addEventListener('change', function(event) {
            const file = event.target.files[0];
            const previewWrapper = document.getElementById('cover_preview_wrapper');
            const previewImage = document.getElementById('cover_preview');

            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    previewWrapper.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            } else {
                previewImage.src = '';
                previewWrapper.classList.add('hidden');
            }
        });
    </script>

</x-app-layout>
