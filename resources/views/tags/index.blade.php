<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Управління тегами') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h1 class="text-2xl font-bold mb-4">Список усіх тегів</h1>

                    @auth
                        @if (auth()->user()->role === 'admin')
                            <a href="{{ route('tests.index') }}"
                               class="bg-blue-500 text-white p-2 rounded mb-4 inline-block">
                                ← Назад до тестів
                            </a>
                        @endif
                    @endauth

                    @auth
                        @if (auth()->user()->role === 'admin')
                            <!-- Форма для створення нового тегу -->
                            <div class="mb-6">
                                <h2 class="text-xl font-bold">Створити новий тег</h2>
                                <form action="{{ route('tags.store') }}" method="POST">
                                    @csrf
                                    <input type="text" name="name" placeholder="Введіть назву нового тегу"
                                           class="px-2 py-1 rounded border border-gray-300 dark:border-gray-600
                                                  bg-white dark:bg-gray-800 text-black dark:text-white w-full"
                                           required />
                                    <button type="submit" class="mt-2 bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm">
                                        💾 Створити тег
                                    </button>
                                </form>
                            </div>
                        @endif
                    @endauth

                    @if ($tags->isEmpty())
                        <p class="text-red-500 font-semibold">Тегів поки що немає.</p>
                    @else
                        <div class="space-y-2">
                            @foreach ($tags as $tag)
                                <div class="bg-gray-100 dark:bg-gray-700 rounded px-4 py-2 flex justify-between items-center">
                                    @auth
                                        @if (auth()->user()->role === 'admin')
                                            <form action="{{ route('tags.update', $tag->id) }}" method="POST" class="flex items-center gap-2 w-full">
                                                @csrf
                                                @method('PUT')
                                                <input type="text" name="name" value="{{ $tag->name }}"
                                                       class="flex-1 px-2 py-1 rounded border border-gray-300 dark:border-gray-600
                                                              bg-white dark:bg-gray-800 text-black dark:text-white" />
                                                <button type="submit"
                                                        class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm">
                                                    💾 Зберегти
                                                </button>
                                            </form>
                                            <form action="{{ route('tags.destroy', $tag->id) }}" method="POST" class="ml-2">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm"
                                                        onclick="return confirm('Ви впевнені, що хочете видалити цей тег?')">
                                                    ❌ Видалити
                                                </button>
                                            </form>
                                        @else
                                            <span>{{ $tag->name }}</span>
                                        @endif
                                    @endauth
                                </div>
                            @endforeach
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
