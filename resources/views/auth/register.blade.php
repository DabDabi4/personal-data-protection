<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Імя')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Пошта')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            @if($errors->has('email'))
    @foreach($errors->get('email') as $message)
        <p class="text-sm text-red-600 mt-1">
            {{ $message === 'The email has already been taken.'
                ? 'Електронна пошта вже зареєстрована.'
                : ($message === 'The email field must be lowercase.'
                    ? 'Пошта повинна бути написана малими літерами.'
                    : $message) }}
        </p>
    @endforeach
@endif


        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Пароль')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            @if($errors->has('password'))
    @foreach($errors->get('password') as $message)
        <p class="text-sm text-red-600 mt-1">
            {{ $message === 'The password field must be at least 8 characters.' 
                ? 'Пароль має бути щонайменше 8 символів.' 
                : $message }}
        </p>
    @endforeach
@endif

        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Підтвердьте пароль')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

           @if($errors->has('password'))
    @foreach($errors->get('password') as $message)
        <p class="text-sm text-red-600 mt-1">
            {{ $message === 'The password field must be at least 8 characters.' 
                ? 'Пароль має бути щонайменше 8 символів.' 
                : $message }}
        </p>
    @endforeach
@endif

        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('login') }}">
                {{ __('Вже зареєстровані?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Зареєструватися') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
