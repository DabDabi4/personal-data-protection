<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Тест: ' . $test->title) }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

                    <h1 class="text-2xl font-bold mb-6">{{ $test->title }}</h1>
                    <p class="text-gray-600 mb-6">{{ $test->description }}</p>

                    @if (count($questions) > 0)
                        <form method="POST" action="{{ route('tests.check') }}">
                            @csrf
                            <input type="hidden" name="test_id" value="{{ $test->id }}">

                            @foreach ($questions as $index => $q)
                                <div class="mb-6">
                                    <h3 class="text-lg font-semibold mb-2">{{ $index + 1 }}. {{ $q['question'] }}</h3>
                                    <div class="ml-4 space-y-1">
                                        @foreach ($q['answers'] as $answerKey => $answerText)
    @if($answerText !== '')
        <label>
            <input type="radio" name="q{{ $index }}" value="{{ $answerKey }}" required>
            {{ $answerText }}
        </label><br>
    @endif
@endforeach

                                    </div>
                                </div>
                            @endforeach

                            <button type="submit" class="bg-blue-600 hover:bg-blue-800 text-white px-4 py-2 rounded">
                                Завершити тест
                            </button>
                        </form>
                    @else
                        <p class="text-red-500">Питання не знайдено або файл не містить даних.</p>
                    @endif

                    <div class="mt-6 flex items-center gap-4">
                        <a href="{{ route('tests.index') }}" class="text-gray-500 hover:text-gray-700">
                            ← Назад до списку тестів
                        </a>

                        @auth
                            @if (auth()->user()->role === 'admin')
                                <a href="{{ route('tests.edit', $test->id) }}" class="text-blue-500 hover:text-blue-700">
                                    ✏️ Редагувати тест
                                </a>

                                <form action="{{ route('tests.destroy', $test->id) }}" method="POST" class="inline-block"
                                      onsubmit="return confirm('Ви впевнені, що хочете видалити цей тест?');">
                                    @csrf
                                    
                                </form>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
