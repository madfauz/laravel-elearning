<x-app-layout>
    <div class="w-1/2 mx-auto my-12">
        <a href="{{ url()->previous() }}" class="text-sm text-gray-600 flex items-center gap-2 mb-6 cursor-pointer">
            <x-bx-arrow-back class="w-4 h-4 text-gray-600" />
            {{ __('Back') }}
        </a>
        <form id="updateCourse" method="POST" action="{{ route('course.update', $course->course_id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div>
                <x-input-label for="title" :value="__('Course Name')" />
                <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title', $course->title)"
                    required autofocus autocomplete="title" />
                <x-input-error :messages="$errors->get('title')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="description" :value="__('description')" />
                <x-text-input id="description" class="block mt-1 w-full" type="text" name="description"
                    :value="old('description', $course->description)" required autofocus autocomplete="description" />
                <x-input-error :messages="$errors->get('description')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="type" :value="__('type')" />
                <select id="type" name="type"
                    class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="public" {{ old('type', $course->type) == 'public' ? 'selected' : '' }}>
                        Public
                    </option>
                    <option value="private" {{ old('type', $course->type) == 'private' ? 'selected' : '' }}>
                        Private
                    </option>
                </select>
                <x-input-error :messages="$errors->get('type')" class="mt-2" />
            </div>

            <div id="access_code_wrapper" class="mt-4 hidden">
                <x-input-label for="access_code" :value="__('Access Code')" />
                <x-text-input id="access_code" class="block mt-1 w-full" type="text" name="access_code"
                    :value="old('access_code' , $course->access_code)" autocomplete="access_code" />
                <x-input-error :messages="$errors->get('access_code')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="cover_path" :value="__('Thumbnail')" />
                <input id="cover_path"
                    class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                    type="file" name="cover_path" accept="image/*" />

                <div id="cover_preview_wrapper" class="mt-2">
                    <p class="text-sm text-gray-500 mb-1">Preview:</p>
                    <img id="cover_preview" src="{{ $course->cover_url }}" alt="Thumbnail Preview" class="max-w-xs rounded shadow" />
                </div>

                <x-input-error :messages="$errors->get('cover_path')" class="mt-2" />
            </div>
        </form>

        @if (Route::has('course.update'))
            <div class="flex items-center justify-end mt-4">
                <x-primary-button x-data @click="$dispatch('open-modal', 'save_confirmation')" class="ml-4">
                    {{ __('Save') }}
                </x-primary-button>
                <x-confirm-modal id="save_confirmation" message="Are you sure you want to update Course?"
                    okLabel="Save" cancelLabel="Cancel" :url="route('course.update', $course->course_id)" method="PUT" formId="updateCourse" />
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


        document.getElementById('cover_path').addEventListener('change', function (event) {
        const file = event.target.files[0];
        const previewWrapper = document.getElementById('cover_preview_wrapper');
        const previewImage = document.getElementById('cover_preview');

        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function (e) {
                previewImage.src = e.target.result;
            };
            reader.readAsDataURL(file);
        } else {
            previewImage.src = '';
        }
    });
    </script>

</x-app-layout>
