<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Курс "Захист персональних даних"</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        .hero-gradient {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50 dark:bg-gray-900">

<!-- Header -->
<header class="hero-gradient text-white shadow-lg">
    <div class="container mx-auto px-6 py-16 text-center">
        <h1 class="text-4xl font-bold mb-4">Захист персональних даних</h1>
        <p class="text-xl mb-8">Онлайн-курс, який допоможе дізнатися, як захистити особисту інформацію</p>
        <a href="#" class="inline-block bg-white text-indigo-600 font-semibold px-8 py-3 rounded-lg hover:bg-gray-100 transition duration-300 shadow-md">
            Почати навчання
        </a>
    </div>
</header>

<!-- Benefits Section -->
<section class="bg-gray-100 dark:bg-gray-800 py-12">
    <div class="container mx-auto px-6">
        <div class="max-w-4xl mx-auto text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-800 dark:text-white">Курс допоможе зрозуміти:</h2>
        </div>

        <div class="grid md:grid-cols-2 gap-6 max-w-6xl mx-auto">
            @php
                $benefits = [
                    'коли погодитися на обробку даних, а коли можна й відмовитися',
                    'хто матиме доступ до вашої інформації і що робити, якщо її використовують у неприйнятний спосіб',
                    'як розпізнавати, де лишати свою дату народження, а де — ні',
                    'як видалити свої дані або змінити, якщо є помилка',
                    'та інші поради для захисту особистої інформації'
                ];
            @endphp
            @foreach ($benefits as $benefit)
                <div class="benefit-card bg-white dark:bg-gray-700 p-6 rounded-lg shadow-md {{ $loop->last ? 'md:col-span-2' : '' }}">
                    <div class="flex items-start">
                        <svg class="w-6 h-6 text-indigo-600 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <p class="text-gray-700 dark:text-gray-300">{{ $benefit }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Program Section -->
<section class="container mx-auto px-6 py-12" x-data="{ openModule: null }">
    <div class="max-w-4xl mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden border border-gray-200 dark:border-gray-700">
        <div class="p-8">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">Програма курсу</h2>

            @php
                $modules = [
                    ['id' => 1, 'name' => 'Вступ', 'lectures' => [
                        ['name' => 'Що таке персональні дані?', 'description' => 'Огляд основних понять'],
                        ['name' => 'Навіщо їх захищати?', 'description' => 'Причини і наслідки порушення безпеки']
                    ]],
                    ['id' => 2, 'name' => 'Права користувача', 'lectures' => [
                        ['name' => 'Права щодо обробки даних', 'description' => 'Що вам дозволено законом'],
                        ['name' => 'Як подати скаргу?', 'description' => 'Алгоритм дій у разі порушень']
                    ]]
                ];
            @endphp

            <div class="space-y-4">
                @foreach ($modules as $module)
                    <div class="bg-gray-50 dark:bg-gray-700 p-5 rounded-lg border hover:border-indigo-300 dark:border-gray-600 dark:hover:border-indigo-500 shadow-sm cursor-pointer"
                         @click="openModule = openModule === {{ $module['id'] }} ? null : {{ $module['id'] }}">
                        <div class="flex justify-between items-center">
                            <h3 class="font-semibold text-lg text-gray-800 dark:text-white">{{ $module['name'] }}</h3>
                            <svg :class="{ 'rotate-180': openModule === {{ $module['id'] }} }"
                                 class="w-6 h-6 text-gray-500 dark:text-gray-400 transition-transform duration-200"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                        <div x-show="openModule === {{ $module['id'] }}" x-transition class="mt-4 space-y-3 pl-2">
                            @foreach ($module['lectures'] as $lecture)
                                <div class="p-4 bg-white dark:bg-gray-600 rounded-lg border-l-4 border-indigo-500 shadow-sm">
                                    <h4 class="font-medium text-gray-800 dark:text-white">{{ $lecture['name'] }}</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">{{ $lecture['description'] }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="bg-indigo-700 dark:bg-indigo-800 text-white py-12">
    <div class="container mx-auto px-6 text-center">
        <h2 class="text-3xl font-bold mb-6">Готові навчитися захищати свої дані?</h2>
        <a href="#" class="inline-block bg-white text-indigo-600 font-semibold px-8 py-3 rounded-lg hover:bg-gray-100 transition duration-300 shadow-md">
            Почати навчання
        </a>
    </div>
</section>

<!-- Footer -->
<footer class="bg-gray-800 dark:bg-gray-900 text-white py-8">
    <div class="container mx-auto px-6 flex flex-col md:flex-row justify-between items-center">
        <p>© {{ date('Y') }} Курс "Захист персональних даних".</p>
        <div class="flex space-x-4 mt-4 md:mt-0">
            <a href="#" class="px-4 py-2 bg-gray-700 rounded hover:bg-gray-600 transition">Головна</a>
        </div>
    </div>
</footer>

</body>
</html>
