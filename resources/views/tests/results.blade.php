<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Результати тесту: {{ $test->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-xl font-semibold mb-4">Ваші результати:</h3>

                    @if ($results->isEmpty())
                        <p>Ви ще не проходили цей тест.</p>
                    @else
                        <ul class="space-y-2">
                        @foreach ($results as $r)
    <li class="border-b pb-2 border-gray-300 dark:border-gray-700">
        {{ $r->created_at->format('d.m.Y H:i') }} — {{ $r->score }} балів
        <a href="{{ route('tests.result-details', $r->id) }}" class="text-blue-500 hover:underline">Переглянути деталі</a>

    </li>
@endforeach

                        </ul>
                    @endif

                    <a href="{{ route('tests.index') }}" class="inline-block mt-6 text-blue-500 hover:underline">← Назад</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
