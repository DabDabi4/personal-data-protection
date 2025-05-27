<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Редагувати лекцію: {{ $lecture->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 p-6 shadow rounded-lg">
                  <div class="mb-6">
                   <a href="{{ route('theory.show', $lecture) }}" 
                       class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 flex items-center w-max">
                        ⬅️ Назад
                    </a>
                </div>
                <form action="{{ route('lectures.update', $lecture) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Назва</label>
                        <input type="text" name="name" value="{{ $lecture->name }}" required
                               class="mt-1 block w-full rounded border-gray-300 shadow-sm">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Опис</label>
                        <textarea name="description" rows="3"
                                  class="mt-1 block w-full rounded">{{ $lecture->description }}</textarea>
                    </div>
                    

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Замінити файл (.txt або .docx)</label>
                        <input type="file" name="content_file" accept=".txt,.docx" class="w-full p-2 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white">

                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Замінити відео (mp4, webm, ogg)</label>
                       <input type="file" name="video" accept="video/mp4,video/webm,video/ogg" class="w-full p-2 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    </div>

                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        Зберегти зміни
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
