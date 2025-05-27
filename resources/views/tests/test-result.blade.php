<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Результати тесту') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h1 class="text-2xl font-bold mb-4">Ваш результат</h1>
                    <p>Ви відповіли вірно на {{ $score }} з {{ $total }} запитань.</p>

                    @if ($score == $total)
                        <p class="text-green-600">Чудово! Ви правильно відповіли на всі питання!</p>
                    @elseif ($score >= $total / 2)
                        <p class="text-yellow-600">Добре, але є місце для покращень.</p>
                    @else
                        <p class="text-red-600">Потрібно більше працювати над матеріалом.</p>
                    @endif

                    <button class="mt-6 py-2 px-4 bg-blue-500 text-white rounded-md hover:bg-blue-700" onclick="window.location.href='/tests'">Пройти тест знову</button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
