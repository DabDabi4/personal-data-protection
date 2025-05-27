<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Лекція:') }} {{ $lecture->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900 dark:text-gray-100">
                 <div class="flex gap-4 mb-10">
    <a href="{{ route('theory.index') }}" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
        ⬅️ Назад
    </a>
    @if(auth()->user()?->role === 'admin')
    <a href="{{ route('lectures.edit', $lecture) }}" class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600">
        ✏️ Редагувати
    </a>
    @endif
</div>
                <h1 class="text-2xl font-bold mb-4">{{ $lecture->name }}</h1>

                @if ($lecture->description)
                    <p class="mb-4 text-gray-700 dark:text-gray-300">{{ $lecture->description }}</p>
                @endif

                {{-- Відображення тексту з .txt файлу як Markdown --}}
@if ($lecture->file_url)
    @php
        $filePath = storage_path('app/public/' . $lecture->file_url);
        $ext = pathinfo($filePath, PATHINFO_EXTENSION);
    @endphp

    @if ($ext === 'docx' && file_exists($filePath))
        @php
            
            $phpWord = \PhpOffice\PhpWord\IOFactory::load($filePath);
           
            $xmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'HTML');


            ob_start();
            $xmlWriter->save('php://output');
            $html = ob_get_clean();
        @endphp
  <style>
        .lecture-text table {
            background-color: white !important;
            color: black !important;
            border-collapse: collapse;
            width: 100%;
        }
        .lecture-text table th,
        .lecture-text table td {
            border: 1px solid #ddd;
            padding: 8px;
            background-color: white !important;
            color: black !important;
        }
        .lecture-text table tr:hover {
            background-color: #f1f1f1;
        }
    </style>
        <div class="lecture-text prose dark:prose-invert max-w-none mt-4">
            {!! $html !!}
        </div>
    @elseif ($ext === 'txt' && file_exists($filePath))
        @php
            $text = file_get_contents($filePath);
        @endphp
        <div class="lecture-text prose dark:prose-invert max-w-none mt-4">
            {!! nl2br(e($text)) !!}
        </div>
    @endif
@endif




                {{-- Відображення відео --}}
@if ($lecture->video_url && \Illuminate\Support\Str::endsWith($lecture->video_url, ['.mp4', '.webm', '.ogg']))
    <div class="mb-6">
        <h3 class="font-semibold">Відео:</h3>
        <video controls class="w-full rounded mt-2">
    <source src="{{ route('lectures.stream', $lecture) }}" type="video/mp4">
    Ваш браузер не підтримує відео.
</video>

    </div>
@elseif ($lecture->video_url)
    <div class="mb-6">
        <h3 class="font-semibold">Відео:</h3>
        {{-- Якщо це зовнішнє посилання, виводимо iframe --}}
        <div class="aspect-w-16 aspect-h-9 mt-2">
            <iframe class="w-full h-96" src="{{ $lecture->video_url }}" frameborder="0" allowfullscreen></iframe>
        </div>
    </div>
@endif
@if ($lecture->file_url)
    <div class="mt-6">
        <a href="{{ asset('storage/' . $lecture->file_url) }}" 
           download 
           class="inline-block px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
            ⬇️ Завантажити файл
        </a>
    </div>
@endif
@if ($lecture->video_url && \Illuminate\Support\Str::endsWith($lecture->video_url, ['.mp4', '.webm', '.ogg']))
        {{-- Кнопка завантаження відео --}}
        <div class="mt-4">
            <a href="{{ route('lectures.stream', $lecture) }}" 
               download 
               class="inline-block px-4 py-2 bg-purple-500 text-white rounded hover:bg-purple-600">
                ⬇️ Завантажити відео
            </a>
        </div>
    </div>
@endif
@if ($nextLecture)
    <div class="mt-8">
        <a href="{{ route('theory.show', $nextLecture) }}" 
           class="inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            👉 Наступна лекція: {{ $nextLecture->name }}
        </a>
    </div>
@endif
@if ($test)
    <div class="mt-10 p-6 bg-indigo-100 dark:bg-indigo-900 rounded shadow">
        <h2 class="text-xl font-bold mb-4 text-indigo-800 dark:text-indigo-200">📝 Тест за темою лекції</h2>

        <h3 class="text-xl font-bold mb-4 text-indigo-800 dark:text-indigo-200">{{ $test->title }}</h3>

        <a href="{{ route('tests.show', $test) }}"
           class="inline-block px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
            👉 Перейти до тесту
        </a>
    </div>
@endif


            </div>
        </div>
    </div>
</x-app-layout>
