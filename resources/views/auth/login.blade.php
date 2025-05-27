<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Пошта')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
           @if($errors->has('email'))
    @foreach($errors->get('email') as $message)
        <p class="text-sm text-red-600 mt-2">
            {{ $message === 'These credentials do not match our records.'
                ? 'Ці облікові дані не відповідають нашим записам.'
                : $message }}
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
                            required autocomplete="current-password" />

            @if($errors->has('email'))
    @foreach($errors->get('email') as $message)
        <p class="text-sm text-red-600 mt-2">
            {{ $message === 'These credentials do not match our records.'
                ? 'Ці облікові дані не відповідають нашим записам.'
                : $message }}
        </p>
    @endforeach
@endif

        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" name="remember">
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Памятай мене') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-center mt-4">

            <x-primary-button class="ms-3">
                {{ __('Вхід') }}
            </x-primary-button>
        </div>
        <div class="mt-4 text-center">
    <a href="{{ route('register') }}" class="underline text-sm text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200">
        {{ __('Немає облікового запису? Зареєструватися') }}
    </a>
</div>
    </form>
</x-guest-layout>
