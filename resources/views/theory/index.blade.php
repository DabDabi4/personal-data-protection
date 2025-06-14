<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Теорія') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="moduleState()" x-init="init()">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900 dark:text-gray-100">
                 <form method="GET" action="{{ route('theory.index') }}" class="mb-6 space-y-2" id="search-form">
    <input type="text" name="module_search" value="{{ request('module_search') }}"
        placeholder="Пошук модулів..."
        class="p-2 rounded w-full md:w-1/2 bg-gray-100 dark:bg-gray-700 text-black dark:text-white"
        oninput="document.getElementById('search-form').submit();">
    
    <input type="text" name="lecture_search" value="{{ request('lecture_search') }}"
        placeholder="Пошук лекцій..."
        class="p-2 rounded w-full md:w-1/2 bg-gray-100 dark:bg-gray-700 text-black dark:text-white"
        oninput="document.getElementById('search-form').submit();">
        <select name="tag_search" onchange="document.getElementById('search-form').submit();"
    class="p-2 rounded w-full md:w-1/2 bg-gray-100 dark:bg-gray-700 text-black dark:text-white">
    <option value="">-- Виберіть тег --</option>
    @foreach ($tags as $tag)
        <option value="{{ $tag->name }}" {{ request('tag_search') == $tag->name ? 'selected' : '' }}>
            {{ $tag->name }}
        </option>
    @endforeach
</select>
</form>
                <h1 class="text-2xl font-bold mb-4">Модулі</h1>
                {{-- Кнопка створення модуля --}}
            @if(auth()->user()?->role === 'admin')
    <button onclick="document.getElementById('create-form').classList.toggle('hidden')" class="mb-4 py-2 px-4 bg-green-600 text-white rounded hover:bg-green-700">
        ➕ Створити модуль
    </button>

    <div id="create-form" class="hidden mb-6">
        <form method="POST" action="{{ route('modules.store') }}">
            @csrf
            <div class="mb-2">
                <label for="name" class="block text-sm font-medium text-gray-200">Назва модуля</label>
                <input type="text" name="name" id="name" class="w-full mt-1 p-2 rounded bg-gray-700 text-white" required>
            </div>
            <button type="submit" class="mt-2 py-2 px-4 bg-blue-500 text-white rounded hover:bg-blue-600">
                💾 Зберегти
            </button>
        </form>
    </div>
@endif

                {{-- Список модулів --}}
                @foreach ($modules as $module)
                   <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg shadow mb-4" x-data="{ editModule: false }">
    {{-- Назва модуля --}}
    <div class="flex justify-between items-center mb-2">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $module->name }}</h3>
       @if(auth()->user()?->role === 'admin')
    <button @click="editModule = !editModule" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm">
        ✏️ Редагувати
    </button>
@endif

    </div>

    {{-- Форма редагування модуля --}}
    <form method="POST" action="{{ route('modules.update', $module->id) }}" x-show="editModule" x-transition>
        @csrf
        @method('PATCH')
        <input
            type="text"
            name="name"
            value="{{ $module->name }}"
            class="w-full mb-2 p-2 rounded bg-gray-200 dark:bg-gray-600 text-black dark:text-white"
            required
        />
        <div class="flex justify-end">
            <button type="submit" class="mr-2 py-1 px-3 bg-blue-500 text-white rounded hover:bg-blue-600">💾 Зберегти</button>
            <button type="button" @click="editModule = false" class="py-1 px-3 bg-gray-400 text-white rounded hover:bg-gray-500">❌ Скасувати</button>
        </div>
    </form>
                        <div>
                           <div class="flex justify-between items-center cursor-pointer" @click="toggleModule({{ $module->id }})">

                                <span class="text-sm text-gray-500 dark:text-gray-400">Лекції модуля</span>
                                <svg :class="{ 'rotate-180': openModule === {{ $module->id }} }" class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>

                            {{-- Лекції --}}
                            <div x-show="openModule === {{ $module->id }}" x-transition class="mt-4 space-y-3">
                                {{-- Кнопка створення лекції --}}
                                @if(auth()->user()?->role === 'admin')
                                <button @click.prevent="showCreateLectureForm === {{ $module->id }} ? showCreateLectureForm = null : showCreateLectureForm = {{ $module->id }}" class="mb-3 py-1 px-3 bg-green-600 text-white rounded hover:bg-green-700">
    ➕ Створити лекцію
</button>

                                @endif
                                {{-- Форма створення лекції --}}
                                <div x-show="showCreateLectureForm === {{ $module->id }}" x-transition class="mb-4 p-4 bg-gray-200 dark:bg-gray-600 rounded">
                                    <form id="lecture-form-{{ $module->id }}" action="{{ route('lectures.store', $module->id) }}" enctype="multipart/form-data" x-data="{ lectureContentType: 'text' }">
                                        @csrf
                                        <div class="mb-2">
                                            <label for="name-{{ $module->id }}" class="block text-sm font-medium">Назва лекції</label>
                                            <input type="text" id="name-{{ $module->id }}" name="name" required class="w-full p-2 rounded bg-white text-black">
                                        </div>

                                        <div class="mb-2">
                                            <label for="description-{{ $module->id }}" class="block text-sm font-medium">Опис</label>
                                            <textarea id="description-{{ $module->id }}" name="description" rows="3" class="w-full p-2 rounded bg-white text-black"></textarea>
                                        </div>
                                        <div class="mb-4" x-data="{ selectedTags: [] }">
    <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">Теги</label>
    {{-- Вибір і кнопка --}}
    <div class="flex items-center space-x-2">
        <select id="tagSelect-{{ $module->id }}" class="block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600
               text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-700 focus:ring focus:ring-indigo-200">
            <option value="">-- Виберіть тег --</option>
    @foreach ($tags as $tag)
        <option value="{{ $tag->id }}">{{ $tag->name }}</option>
    @endforeach
</select>
        </select>
        <button type="button" id="addTagBtn-{{ $module->id }}"
            class="bg-indigo-600 hover:bg-indigo-800 text-white px-3 py-1 rounded">
            + Додати
        </button>
    </div>
    {{-- Обрані теги --}}
    <div id="selectedTagsContainer-{{ $module->id }}" class="flex flex-wrap gap-2 mt-3">
        <!-- Динамічно додані теги -->
    </div>
</div>
                                        <div class="mb-2">
                                            <label class="block text-sm font-medium mb-1">Текст лекції</label>
                                            <div class="flex items-center gap-4 mb-2">
                                                <label class="flex items-center">
                                                    <input type="radio" name="content_type" value="text" x-model="lectureContentType" checked class="mr-1"> Ввести текст
                                                </label>
                                                <label class="flex items-center">
                                                    <input type="radio" name="content_type" value="file" x-model="lectureContentType" class="mr-1"> Завантажити файл (.txt, .docx)
                                                </label>
                                            </div>
                                            <div x-show="lectureContentType === 'text'" class="mt-2">
                                                <textarea name="content" rows="8" class="w-full p-2 rounded bg-white text-black" placeholder="Введіть текст лекції тут..."></textarea>
                                            </div>
                                            <div x-show="lectureContentType === 'file'" class="mt-2">
                                                <input type="file" name="content_file" accept=".txt,.docx" class="w-full p-2 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                            </div>
                                        </div>
<div x-data="{ videoType: 'file' }" class="mb-4">
    {{-- Перемикач --}}
    <div class="mb-2">
        <label class="block font-semibold mb-1">Обрати тип відео:</label>
        <select x-model="videoType" class="w-full p-2 rounded border text-black">
            <option value="file">📁 Завантажити файл</option>
            <option value="url">🌐 Посилання на відео</option>
        </select>
    </div>

    {{-- Завантаження файлу --}}
    <div class="mb-2" x-show="videoType === 'file'" x-cloak>
        <label for="video-{{ $module->id }}" class="block mb-1">Відео (mp4, webm, ogg)</label>
        <input type="file" id="video-{{ $module->id }}" name="video" class="w-full p-2 rounded border" accept="video/*">
    </div>

    {{-- Посилання на відео --}}
    <div class="mb-2" x-show="videoType === 'url'" x-cloak>
        <label for="video_url-{{ $module->id }}" class="block mb-1">Посилання на відео (Google Drive) ось приклад https://drive.google.com/file/d/.../preview</label>
        <input type="url" id="video_url-{{ $module->id }}" name="video_url" class="w-full p-2 rounded bg-white text-black" placeholder="https://drive.google.com/...">
    </div>
</div>


                                        <div class="mb-4">
                                            <progress id="progress-bar-{{ $module->id }}" value="0" max="100" class="w-full h-4 hidden"></progress>
                                            <p id="progress-text-{{ $module->id }}" class="text-sm text-blue-500 mt-1 hidden">0%</p>
                                        </div>

                                        <button type="submit" class="py-2 px-4 bg-blue-500 text-white rounded hover:bg-blue-600">
                                            💾 Завантажити лекцію
                                        </button>
                                    </form>
                                </div>

                                {{-- Лекції модуля --}}
                                @php $lectures = $module->lectures->sortBy('order'); @endphp
                                @forelse ($lectures as $lecture)
                                   <div class="p-3 bg-gray-300 dark:bg-gray-500 rounded">
    <div class="flex justify-between items-center">
        <div>
            <h4 class="font-medium">{{ $lecture->name }}</h4>
            @if ($lecture->description)
                <p class="text-sm text-gray-800 dark:text-gray-300">{{ $lecture->description }}</p>
            @endif

            {{-- Вивід тегів --}}
            @if ($lecture->tags->count())
                <div class="mt-2 flex flex-wrap gap-2">
                   @foreach ($lecture->tags as $tag)
    <form method="POST" action="{{route('lectures.tags.detach', [$lecture->id, $tag->id]) }}" class="inline-block">
        @csrf
        @method('DELETE')
        <span class="flex items-center bg-indigo-600 text-white text-xs px-2 py-1 rounded-full hover:bg-red-600 transition group">
            {{ $tag->name }}
                @if(auth()->user()?->role === 'admin')
            <button type="submit" class="ml-2 text-white hover:text-red-300 opacity-0 group-hover:opacity-100 transition-opacity">
                &times;
            </button>
            @endif
        </span>
    </form>
@endforeach

                </div>
            @endif
            {{-- Кнопка додати тег --}}
            @if(auth()->user()?->role === 'admin')
<button onclick="openTagForm({{ $lecture->id }})" class="mt-2 text-sm text-white-700 hover:text-blue-900 ">
    ➕ Додати тег
</button>
@endif

{{-- Форма вибору тегу (спочатку прихована) --}}
<div id="tag-form-{{ $lecture->id }}" class="mt-2 hidden">
    <form method="POST" action="{{ route('lectures.tags.attach', $lecture->id) }}">
        @csrf
        <select name="tag_id" class="rounded p-1 bg-white text-black">
            @foreach ($tags as $tag)
                <option value="{{ $tag->id }}">{{ $tag->name }}</option>
            @endforeach
        </select>
        <button type="submit" class="ml-2 px-2 py-1 bg-green-600 text-white rounded hover:bg-green-700">Додати</button>
    </form>
</div>

        </div>

        <div class="flex gap-2 items-center">
            {{-- Вгору --}}
            <form method="POST" action="{{ route('lectures.move', $lecture) }}">
                @csrf
                <input type="hidden" name="direction" value="up">
                @if(auth()->user()?->role === 'admin')
                <button class="text-blue-500 hover:text-blue-700" title="Вгору">⬆️</button>
                @endif
            </form>
            {{-- Вниз --}}
            <form method="POST" action="{{ route('lectures.move', $lecture) }}">
                @csrf
                <input type="hidden" name="direction" value="down">
                @if(auth()->user()?->role === 'admin')
                <button class="text-blue-500 hover:text-blue-700" title="Вниз">⬇️</button>
                @endif
            </form>
            {{-- Перейти --}}
            <a href="{{ route('lectures.show', $lecture->id) }}" class="text-black-600 border border-blue-600 px-3 py-1 rounded-full hover:bg-blue-600 hover:text-white transition">
                Перейти до лекції
            </a>
            {{-- Видалити --}}
            @if(auth()->user()?->role === 'admin')
            <form action="{{ route('lectures.destroy', $lecture->id) }}" method="POST" onsubmit="return confirm('Ви впевнені, що хочете видалити лекцію?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-600 hover:text-red-800 dark:hover:text-red-400">
                    🗑️
                </button>
            </form>
            @endif
        </div>
    </div>
</div>

                                @empty
                                    <p class="text-sm text-gray-500">Немає лекцій у цьому модулі.</p>
                                @endforelse
                            </div>
                        </div>

                        {{-- Видалити модуль --}}
                        <form action="{{ route('modules.destroy', $module->id) }}" method="POST" onsubmit="return confirm('Ви впевнені, що хочете видалити модуль?')" class="mt-2">
                            @csrf
                            @method('DELETE')
@if(auth()->user()?->role === 'admin')
                            <button type="submit" class="text-red-600 hover:text-red-800 dark:hover:text-red-400">
                                🗑️ Видалити
                            </button>
                            @endif
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const forms = document.querySelectorAll('form[id^="lecture-form-"]');
            forms.forEach(form => {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    const moduleId = form.id.split('-').pop();
                    const progressBar = document.getElementById('progress-bar-' + moduleId);
                    const progressText = document.getElementById('progress-text-' + moduleId);
                    const formData = new FormData(form);
                    const xhr = new XMLHttpRequest();

                    xhr.open('POST', form.action, true);
                    xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('input[name="_token"]').value);

                    xhr.upload.addEventListener('progress', function (e) {
                        if (e.lengthComputable) {
                            const percent = Math.round((e.loaded / e.total) * 100);
                            progressBar.value = percent;
                            progressText.textContent = percent + '%';
                            progressBar.classList.remove('hidden');
                            progressText.classList.remove('hidden');
                        }
                    });

                    xhr.onload = function () {
                        if (xhr.status === 200 || xhr.status === 302) {
                            alert('Лекцію завантажено!');
                            location.reload();
                        } else {
                            alert('Помилка при завантаженні. Статус: ' + xhr.status);
                        }
                    };

                    xhr.onerror = function () {
                        alert('Помилка при підключенні до сервера.');
                    };

                    xhr.send(formData);
                });
            });
        });
    </script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('form[id^="lecture-form-"]').forEach(form => {
    const moduleId = form.id.split('-').pop();
    const tagSelect = form.querySelector(`#tagSelect-${moduleId}`);
    const addTagBtn = form.querySelector(`#addTagBtn-${moduleId}`);
    const selectedTagsContainer = form.querySelector(`#selectedTagsContainer-${moduleId}`);

    const selectedTagIds = new Set();

    addTagBtn?.addEventListener('click', () => {
        const selectedOption = tagSelect.options[tagSelect.selectedIndex];
        const tagId = selectedOption.value;
        const tagName = selectedOption.text;

        if (!tagId || selectedTagIds.has(tagId)) return;

        selectedTagIds.add(tagId);

        const tagBadge = document.createElement('div');
        tagBadge.className = 'bg-indigo-100 dark:bg-indigo-800 text-indigo-800 dark:text-white px-3 py-1 rounded-full flex items-center space-x-2';
        tagBadge.innerHTML = `
            <span>${tagName}</span>
            <button type="button" class="ml-2 text-red-600 hover:text-red-800" data-id="${tagId}">&times;</button>
            <input type="hidden" name="tags[]" value="${tagId}">
        `;

        tagBadge.querySelector('button').addEventListener('click', function () {
            selectedTagIds.delete(this.dataset.id);
            tagBadge.remove();
        });

        selectedTagsContainer.appendChild(tagBadge);
    });
});


        const selectedTagIds = new Set();

        addTagBtn.addEventListener('click', () => {
            const selectedOption = tagSelect.options[tagSelect.selectedIndex];
            const tagId = selectedOption.value;
            const tagName = selectedOption.text;

            if (!tagId || selectedTagIds.has(tagId)) return;

            selectedTagIds.add(tagId);

            // Створення бейджу з тегом
            const tagBadge = document.createElement('div');
            tagBadge.className = 'bg-indigo-100 dark:bg-indigo-800 text-indigo-800 dark:text-white px-3 py-1 rounded-full flex items-center space-x-2';

            tagBadge.innerHTML = `
                <span>${tagName}</span>
                <button type="button" class="ml-2 text-red-600 hover:text-red-800" data-id="${tagId}">&times;</button>
                <input type="hidden" name="tags[]" value="${tagId}">
            `;

            // Обробка видалення тегу
            tagBadge.querySelector('button').addEventListener('click', function () {
                selectedTagIds.delete(this.dataset.id);
                tagBadge.remove();
            });

            selectedTagsContainer.appendChild(tagBadge);
        });
    });
</script>
<script>
    function openTagForm(lectureId) {
        const form = document.getElementById('tag-form-' + lectureId);
        form.classList.toggle('hidden');
    }
</script>
<script>
    function moduleState() {
        return {
            openModule: null,
            showCreateLectureForm: null,

            init() {
                const savedModule = localStorage.getItem('openModule');
                if (savedModule) {
                    this.openModule = Number(savedModule);
                }

                const savedLectureForm = localStorage.getItem('showCreateLectureForm');
                if (savedLectureForm) {
                    this.showCreateLectureForm = Number(savedLectureForm);
                }
            },

            toggleModule(id) {
                this.openModule = this.openModule === id ? null : id;
                localStorage.setItem('openModule', this.openModule ?? '');
            },

            toggleCreateLectureForm(id) {
                this.showCreateLectureForm = this.showCreateLectureForm === id ? null : id;
                localStorage.setItem('showCreateLectureForm', this.showCreateLectureForm ?? '');
            }
        }
    }
</script>

</x-app-layout>
