<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Деталі результатів тесту: {{ $result->test->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-xl font-semibold mb-4">Ваш результат: {{ $result->score }} балів</h3>

                    @php
                        $attemptCount = $result->user->results->where('test_id', $result->test_id)->count();
                    @endphp

                    <ul class="space-y-4">
                        @foreach ($questions as $index => $question)
                            <li class="border-b pb-4">
                                <p><strong>Питання {{ $index + 1 }}:</strong> {{ $question['question'] }}</p>
                                <p><strong>Ваша відповідь:</strong> {{ $question['answers'][$question['user_answer']] ?? 'Невірна відповідь' }}</p>

                                @if ($attemptCount >= 3)
                                    <p><strong>Правильна відповідь:</strong> {{ $question['correct'] }}</p>
                                @endif

                                <ul class="list-inside ml-4">
                                    @foreach ($question['answers'] as $key => $answer)
                                        <li>{{ $key }}) {{ $answer }}</li>
                                    @endforeach
                                </ul>
                            </li>
                        @endforeach
                    </ul>

                    <a href="{{ route('tests.results', $result->test->id) }}" class="inline-block mt-6 text-blue-500 hover:underline">
                        ← Назад до результатів
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
