@php
    use App\Models\Setting;
       use App\Models\Test
@endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Тести') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h1 class="text-2xl font-bold mb-4">Тестування на тему "Захист персональних даних"</h1>

                    {{-- Пошук і фільтрація --}}
                  <form id="filter-form" method="GET" action="{{ route('tests.index') }}" class="mb-6 flex flex-wrap gap-4 items-center">
    <input type="text" name="search" placeholder="Пошук за назвою..." value="{{ request('search') }}"
           class="p-2 rounded border-gray-300 dark:bg-gray-700 dark:text-white" />

    <select name="tag" class="p-2 rounded border-gray-300 dark:bg-gray-700 dark:text-white">
        <option value="">Всі теги</option>
        @foreach ($allTags as $tag)
            <option value="{{ $tag->id }}" {{ request('tag') == $tag->id ? 'selected' : '' }}>
                {{ $tag->name }}
            </option>
        @endforeach
    </select>
</form>


                    @auth
                        @if (auth()->user()->role === 'admin')
                            <a href="{{ route('tests.create') }}" class="bg-green-500 text-white p-2 rounded mb-4 inline-block">
                                + Створити новий тест
                            </a>
                            <a href="{{ route('tags.index') }}"
   class="bg-indigo-500 text-white p-2 rounded mb-4 inline-block ml-2">
    🏷️ Теги
</a>

                        @endif
                    @endauth
@php
   $userId = auth()->id();
    $threshold = Setting::get('certificate_threshold', 10); // значення за замовчуванням

    $bestScores = \App\Models\TestResult::selectRaw('MAX(score) as max_score, test_id')
        ->where('user_id', $userId)
        ->groupBy('test_id')
        ->pluck('max_score');

    $totalScore = $bestScores->sum();
    $progressPercent = min(100, round(($totalScore / $threshold) * 100));
@endphp
@if (auth()->user()->role === 'admin')
    @if(session('success'))
        <p class="text-green-500 mb-2">{{ session('success') }}</p>
    @endif
    @if(session('error'))
    <p class="text-red-500 mb-2">{{ session('error') }}</p>
@endif
    <form method="POST" action="{{ route('settings.update.threshold') }}" class="mb-6">
        @csrf
        <label for="threshold" class="block mb-1 text-sm">Поріг для сертифіката:</label>
        <input type="number" name="threshold" id="threshold" value="{{ $threshold }}"
               class="p-2 border rounded dark:bg-gray-700 dark:text-white">
        <button type="submit" class="ml-2 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            💾 Зберегти
        </button>
    </form>
@endif

@php


    $allTests = Test::all();
    $totalQuestions = 0;

    foreach ($allTests as $test) {
        if ($test->file_url) {
            $filePath = storage_path('app/public/' . $test->file_url);
            if (file_exists($filePath)) {
                $content = file_get_contents($filePath);
                $totalQuestions += substr_count($content, '*');
            }
        }
    }
@endphp

<div class="mb-4 p-4 bg-yellow-100 text-yellow-900 rounded">
    📊 Усього питань у всіх тестах: <strong>{{ $totalQuestions }}</strong>
</div>

@php
    // Попередження, якщо поріг вищий, ніж реальна кількість питань
    $shouldWarnAboutThreshold = $threshold > $totalQuestions;
@endphp

@if (auth()->user()->role === 'admin' && $shouldWarnAboutThreshold)
    <div class="mb-4 p-4 bg-red-100 text-red-900 rounded border border-red-300">
        ⚠️ Поріг сертифіката ({{ $threshold }} балів) перевищує кількість питань у тестах ({{ $totalQuestions }}).
        <br>Рекомендуємо <strong>оновити поріг</strong> вручну.
    </div>
@endif


<div class="mb-6">
    <h3 class="text-lg font-semibold mb-1">Прогрес до сертифіката:</h3>
    <div class="w-full bg-gray-300 dark:bg-gray-700 rounded-full h-4">
        <div class="bg-green-500 h-4 rounded-full transition-all duration-500 ease-in-out"
             style="width: {{ $progressPercent }}%">
        </div>
    </div>
    <p class="mt-1 text-sm text-gray-700 dark:text-gray-300">
        {{ $totalScore }} / {{ $threshold }} балів ({{ $progressPercent }}%)
    </p>

    @if($progressPercent >= 70)
        <p class="mt-2 text-green-600 dark:text-green-400 font-bold">
            🎉 Вітаємо! Ви отримали сертифікат!
        </p>
         <a href="{{ route('certificate.download') }}"
       class="mt-3 inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
        ⬇️ Завантажити сертифікат
    </a>
    @endif
</div>

                    @if ($tests->isEmpty())
                        <p class="text-red-500 font-semibold">Тестів немає.</p>
                    @else
                        <form action="/submit-test" method="POST">
                            @csrf

                            @foreach ($tests as $index => $test)
                                <div class="flex justify-between items-center mb-4">
                                    <div>
                                        <h2 class="text-xl font-semibold mt-6">
                                            <a href="{{ route('tests.show', $test->id) }}">
                                                {{ $index + 1 }}. {{ $test->title }}
                                            </a>
                                        </h2>
                                        <p class="text-gray-700">{{ $test->description }}</p>

                                        {{-- Теги --}}
                                        @if ($test->tags->isNotEmpty())
                                            <div class="flex flex-wrap gap-2 mt-2">
                                                @foreach ($test->tags as $tag)
                                                    <span class="bg-blue-100 text-blue-800 text-sm font-medium px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">
                                                        {{ $tag->name }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>

                                    @auth
                                        @if (auth()->user()->role === 'admin')
                                            <button type="button" onclick="deleteTest({{ $test->id }})"
                                                    class="text-red-500 hover:text-red-700">
                                                🗑️ Видалити
                                            </button>
                                        @endif
                                    @endauth
                                </div>

                                @php
                                    $bestResult = $test->results()->where('user_id', auth()->id())->orderByDesc('score')->first();
                                @endphp

                                @if ($bestResult)
                                    <p>Ваш найкращий результат: {{ $bestResult->score }} балів</p>
                                    <a href="{{ route('tests.results', $test->id) }}" class="text-blue-500 hover:underline">
                                        Всі результати
                                    </a>
                                @else
                                    <p>Ще не пройдено</p>
                                @endif
                            @endforeach
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    {{-- Скрипт видалення тесту --}}
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        function deleteTest(testId) {
            if (confirm('Ви дійсно хочете видалити цей тест?')) {
                axios.delete('/tests/' + testId)
                    .then(() => {
                        alert('Тест успішно видалено!');
                        location.reload();
                    })
                    .catch(() => {
                        alert('Тест успішно видалено!');
                        location.reload();
                    });
            }
        }
    </script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('filter-form');
        const searchInput = form.querySelector('input[name="search"]');
        const tagSelect = form.querySelector('select[name="tag"]');

        let timeout = null;

        
        searchInput.addEventListener('input', function () {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                form.submit();
            }, 500); 
        });

        
        tagSelect.addEventListener('change', function () {
            form.submit();
        });
    });
</script>

</x-app-layout>
