<x-guest-layout>
    <div class="w-full md:w-1/2 h-full p-4 flex flex-col justify-center items-center">

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form class="w-3/4 md:w-2/3 border-[1px] border-gray-300 p-4 rounded shadow-sm" method="POST"
            action="{{ route('password.email') }}">
            @csrf
            <div class="mb-4 text-sm text-gray-600">
                {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
            </div>

            <!-- Email Address -->
            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')"
                    required autofocus />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-primary-button>
                    {{ __('Email Password Reset Link') }}
                </x-primary-button>
            </div>
        </form>
    </div>
    <x-quote-slider class="w-1/2 h-full" />
</x-guest-layout>
