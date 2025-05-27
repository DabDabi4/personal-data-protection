<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Курс "Захист персональних даних"</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    
    <style>
        .hero-gradient {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        }
        .module-item {
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }
        .module-item:hover {
            transform: translateX(5px);
            border-left-color: #4f46e5;
            background-color: rgba(79, 70, 229, 0.05);
        }
        .benefit-card {
            transition: all 0.3s ease;
            border-bottom: 3px solid transparent;
        }
        .benefit-card:hover {
            transform: translateY(-5px);
            border-bottom-color: #4f46e5;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        .check-icon {
            color: #4f46e5;
        }
        .dark .benefit-card {
            background-color: #1e293b;
            color: #f8fafc;
        }
        .dark .benefit-card:hover {
            background-color: #334155;
        }
        
    </style>
</head>
<body class="font-sans antialiased bg-gray-50 dark:bg-gray-900">
    <!-- Header -->
      <header class="hero-gradient text-white shadow-lg">
        <div class="container mx-auto px-6 py-16">
            <div class="max-w-3xl mx-auto text-center">
                <h1 class="text-4xl font-bold mb-4">Захист персональних даних</h1>
                <p class="text-xl mb-8">Онлайн-курс, який допоможе дізнатися, як захистити особисту інформацію</p>
                @guest
                <a href="{{ route('register') }}" class="inline-block bg-white text-indigo-600 font-semibold px-8 py-3 rounded-lg hover:bg-gray-100 transition duration-300 shadow-md">
                    Зареєструватися
                </a>
                @else
                <a href="{{ route('theory.index') }}"class="inline-block bg-white text-indigo-600 font-semibold px-8 py-3 rounded-lg hover:bg-gray-100 transition duration-300 shadow-md">Перейти до курсу</a>
              @endguest
            </div>
        </div>
    </header>

    <!-- Benefits Section -->
      <section class="bg-gray-100 dark:bg-gray-800 py-12">>
        <div class="container mx-auto px-6">
            <div class="max-w-4xl mx-auto text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-800 dark:text-white">Курс допоможе зрозуміти:</h2>
            </div>
            
            <div class="grid md:grid-cols-2 gap-6 max-w-6xl mx-auto">
                <!-- Benefit 1 -->
                <div class="benefit-card bg-white dark:bg-gray-700 p-6 rounded-lg shadow-md">
                    <div class="flex items-start">
                        <svg class="check-icon w-6 h-6 mt-1 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <p class="text-gray-700 dark:text-gray-300">коли погодитися на обробку даних, а коли можна й відмовитися</p>
                    </div>
                </div>
                
               <!-- Benefit 2 -->
                <div class="benefit-card bg-white dark:bg-gray-700 p-6 rounded-lg shadow-md">
                    <div class="flex items-start">
                        <svg class="check-icon w-6 h-6 mt-1 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <p class="text-gray-700 dark:text-gray-300">хто матиме доступ до вашої інформації і що робити, якщо її починають використовувати у неприйнятний для вас спосіб</p>
                    </div>
                </div>
                
                <!-- Benefit 3 -->
                <div class="benefit-card bg-white dark:bg-gray-700 p-6 rounded-lg shadow-md">
                    <div class="flex items-start">
                        <svg class="check-icon w-6 h-6 mt-1 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <p class="text-gray-700 dark:text-gray-300">як розпізнавати ситуації, де лишати свою дату народження чи прізвище, а де це вже зайве</p>
                    </div>
                </div>
                
                <!-- Benefit 4 -->
                <div class="benefit-card bg-white dark:bg-gray-700 p-6 rounded-lg shadow-md">
                    <div class="flex items-start">
                        <svg class="check-icon w-6 h-6 mt-1 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <p class="text-gray-700 dark:text-gray-300">як видалити свої дані або змінити, якщо знайшли в них помилку</p>
                    </div>
                </div>
                
                <!-- Benefit 5 -->
                <div class="benefit-card bg-white dark:bg-gray-700 p-6 rounded-lg shadow-md md:col-span-2">
                    <div class="flex items-start">
                        <svg class="check-icon w-6 h-6 mt-1 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <p class="text-gray-700 dark:text-gray-300">Та інші корисні поради, які допоможуть захистити особисту інформацію</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

   
   <<!-- Program Section -->
<section class="container mx-auto px-6 py-12" x-data="{ openModule: null, showCreateLectureForm: null }">
    <div class="max-w-4xl mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden border border-gray-200 dark:border-gray-700">
        <div class="p-8">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">Програма курсу</h2>

            {{-- Список модулів --}}
            <div class="space-y-4">
                @foreach ($modules as $module)
                <div class="module-item bg-gray-50 dark:bg-gray-700 p-5 rounded-lg border border-gray-200 dark:border-gray-600 hover:border-indigo-300 dark:hover:border-indigo-500 transition-all duration-200 shadow-sm" 
                     @click="openModule = openModule === {{ $module->id }} ? null : {{ $module->id }}">
                    <div class="flex justify-between items-center cursor-pointer">
                        <h3 class="font-semibold text-lg text-gray-800 dark:text-white">{{ $module->name }}</h3>
                        <svg :class="{ 'rotate-180': openModule === {{ $module->id }} }"
                             class="w-6 h-6 text-gray-500 dark:text-gray-400 transition-transform duration-200"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" 
                                  stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>

                    {{-- Лекції модуля --}}
                    <div x-show="openModule === {{ $module->id }}" x-transition class="mt-4 space-y-3 pl-2">
                        @forelse ($module->lectures->sortBy('order') as $lecture)
                            <a href="{{ route('lectures.show', $lecture) }}"
                               class="block p-4 bg-white dark:bg-gray-600 rounded-lg hover:bg-indigo-50 dark:hover:bg-gray-500 shadow-sm transition-all duration-200 border-l-4 border-transparent hover:border-indigo-500">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <h4 class="font-medium text-gray-800 dark:text-gray-100">{{ $lecture->name }}</h4>
                                        @if($lecture->description)
                                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">{{ $lecture->description }}</p>
                                        @endif
                                    </div>
                                    <span class="text-indigo-600 dark:text-indigo-400 text-lg font-bold">→</span>
                                </div>
                            </a>
                        @empty
                            <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-lg text-center">
                                <p class="text-sm text-gray-500 dark:text-gray-400">У цьому модулі поки нема лекцій.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>


    <section class="bg-indigo-700 dark:bg-indigo-800 text-white py-12">
        <div class="container mx-auto px-6 text-center">
            <h2 class="text-3xl font-bold mb-6">Готові навчитися захищати свої дані?</h2>
            @guest
            <a href="{{ route('register') }}" class="inline-block bg-white text-indigo-600 font-semibold px-8 py-3 rounded-lg hover:bg-gray-100 transition duration-300 shadow-md">
                Почати навчання
            </a>
            @else
             <a href="{{ route('theory.index') }}" class="inline-block bg-white text-indigo-600 font-semibold px-8 py-3 rounded-lg hover:bg-gray-100 transition duration-300 shadow-md">Продовжити навчання</a>
             @endguest
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 dark:bg-gray-900 text-white py-8">
        <div class="container mx-auto px-6">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-4 md:mb-0">
                    <p>© {{ date('Y') }} Курс "Захист персональних даних".</p>
                </div>
                <div class="flex space-x-4">
                    @guest
    <a href="{{ route('login') }}" class="px-4 py-2 bg-gray-700 rounded hover:bg-gray-600 transition duration-300">Логін</a>
@else
    <a href="{{ route('theory.index') }}" class="px-4 py-2 bg-gray-700 rounded hover:bg-gray-600 transition duration-300">Мій курс</a>
@endguest
                    <a href="{{ route('theory.index') }}" class="px-4 py-2 bg-indigo-600 rounded hover:bg-indigo-500 transition duration-300">На головну</a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>