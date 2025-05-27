<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Редагувати тест: ' . $test->title) }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('tests.update', $test->id) }}" method="POST" id="testForm">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">Назва тесту</label>
                            <input type="text" name="title"
                                class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600
                                       text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-700 focus:ring focus:ring-indigo-200"
                                value="{{ old('title', $test->title) }}" required>
                        </div>

                        <div class="mb-4">
                            <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">Опис</label>
                            <textarea name="description"
                                class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600
                                       text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-700 focus:ring focus:ring-indigo-200"
                                rows="3">{{ old('description', $test->description) }}</textarea>
                        </div>

                        <h4 class="text-lg font-semibold mb-4">Питання</h4>
                        <div id="questionsContainer">
                            @foreach ($questions as $index => $q)
                                <div class="mb-6 border p-4 rounded-lg bg-gray-50 dark:bg-gray-700 question-block">
                                    <label class="block font-medium text-sm mb-1">Текст питання</label>
                                    <input type="text" name="q[{{ $index }}][text]"
                                        class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600
                                               text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-700 focus:ring focus:ring-indigo-200"
                                        value="{{ $q['text'] }}" required>
<button type="button"
        class="mt-4 bg-red-600 hover:bg-red-800 text-white px-3 py-1 rounded remove-question">
    Видалити питання
</button>
                                    <div class="answers">
                                        @foreach ($q['answers'] as $ansIndex => $ansText)
                                            <div class="answer-item flex items-center gap-2 mt-2">
                                                <label class="block font-medium text-sm w-24">Варіант {{ $ansIndex + 1 }}</label>
                                                <input type="text" name="q[{{ $index }}][answers][]"
                                                    value="{{ $ansText }}"
                                                    class="flex-1 rounded-md shadow-sm border-gray-300 dark:border-gray-600
                                                           text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-700 focus:ring focus:ring-indigo-200"
                                                    required>
                                                <button type="button" class="text-red-500 hover:text-red-700 remove-answer">&times;</button>
                                            </div>
                                        @endforeach
                                        <button type="button" onclick="addAnswer(this, {{ $index }})"
                                            class="mt-2 bg-green-500 hover:bg-green-700 text-white px-2 py-1 rounded">+ Додати варіант</button>
                                    </div>

                                    <label class="block font-medium text-sm mt-2">Правильний варіант (вкажіть номер)</label>
                                    <input type="number" name="q[{{ $index }}][correct]"
                                        class="mt-1 block w-24 rounded-md shadow-sm border-gray-300 dark:border-gray-600
                                               text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-700 focus:ring focus:ring-indigo-200 correct-input"
                                        min="1" value="{{ $q['correctIndex'] }}" required>

                                </div>
                            @endforeach
                        </div>

                        <button type="button" id="addQuestion"
                            class="mt-4 mb-6 bg-green-600 hover:bg-green-800 text-white px-4 py-2 rounded">
                            + Додати питання
                        </button>

                        <div class="mb-4">
                            <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">Теги</label>
                            <div class="flex items-center space-x-2">
                                <select id="tagSelect"
                                    class="block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600
                                           text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-700 focus:ring focus:ring-indigo-200">
                                    <option value="">-- Виберіть тег --</option>
                                    @foreach ($allTags as $tag)
                                        <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                                    @endforeach
                                </select>
                                <button type="button" id="addTagBtn"
                                    class="bg-indigo-600 hover:bg-indigo-800 text-white px-3 py-1 rounded">
                                    + Додати
                                </button>
                            </div>
                            <div id="selectedTags" class="mt-2 flex flex-wrap gap-2">
                                @foreach ($test->tags as $tag)
                                    <div class="flex items-center bg-indigo-100 dark:bg-indigo-700 text-sm px-2 py-1 rounded">
                                        {{ $tag->name }}
                                        <input type="hidden" name="tags[]" value="{{ $tag->id }}">
                                        <button type="button" class="ml-2 text-red-500 remove-tag">&times;</button>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex items-center">
                            <button type="submit"
                                class="bg-blue-600 hover:bg-blue-800 text-white px-4 py-2 rounded">
                                Зберегти зміни
                            </button>

                            <a href="{{ route('tests.show', $test->id) }}"
                               class="inline-block ml-4 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                                ← Назад до тесту
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <template id="questionTemplate">
        <div class="mb-6 border p-4 rounded-lg bg-gray-50 dark:bg-gray-700 question-block">
            <label class="block font-medium text-sm mb-1">Текст питання</label>
            <input type="text" name="q[__INDEX__][text]"
                class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600
                       text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-700 focus:ring focus:ring-indigo-200"
                required>
                <button type="button"
        class="mt-4 bg-red-600 hover:bg-red-800 text-white px-3 py-1 rounded remove-question">
    Видалити питання
</button>

            <div class="answers">
                <div class="answer-item flex items-center gap-2 mt-2">
                    <label class="block font-medium text-sm w-24">Варіант 1</label>
                    <input type="text" name="q[__INDEX__][answers][]"
                        class="flex-1 rounded-md shadow-sm border-gray-300 dark:border-gray-600
                               text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-700 focus:ring focus:ring-indigo-200"
                        required>
                    <button type="button" class="text-red-500 hover:text-red-700 remove-answer">&times;</button>
                </div>

                <div class="answer-item flex items-center gap-2 mt-2">
                    <label class="block font-medium text-sm w-24">Варіант 2</label>
                    <input type="text" name="q[__INDEX__][answers][]"
                        class="flex-1 rounded-md shadow-sm border-gray-300 dark:border-gray-600
                               text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-700 focus:ring focus:ring-indigo-200"
                        required>
                    <button type="button" class="text-red-500 hover:text-red-700 remove-answer">&times;</button>
                </div>

                <div class="answer-item flex items-center gap-2 mt-2">
                    <label class="block font-medium text-sm w-24">Варіант 3</label>
                    <input type="text" name="q[__INDEX__][answers][]"
                        class="flex-1 rounded-md shadow-sm border-gray-300 dark:border-gray-600
                               text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-700 focus:ring focus:ring-indigo-200"
                        required>
                    <button type="button" class="text-red-500 hover:text-red-700 remove-answer">&times;</button>
                </div>

                <button type="button" onclick="addAnswer(this, __INDEX__)"
                    class="mt-2 bg-green-500 hover:bg-green-700 text-white px-2 py-1 rounded">+ Додати варіант</button>
            </div>

            <label class="block font-medium text-sm mt-2">Правильний варіант (вкажіть номер)</label>
            <input type="number" name="q[__INDEX__][correct]"
                class="mt-1 block w-24 rounded-md shadow-sm border-gray-300 dark:border-gray-600
                       text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-700 focus:ring focus:ring-indigo-200 correct-input"
                min="1" value="1" required>
        </div>
    </template>

    <script>
    document.getElementById('addQuestion').addEventListener('click', function () {
        let container = document.getElementById('questionsContainer');
        let template = document.getElementById('questionTemplate').innerHTML;
        let index = container.children.length;
        let html = template.replace(/__INDEX__/g, index);
        container.insertAdjacentHTML('beforeend', html);
    });

    document.getElementById('testForm').addEventListener('submit', function (e) {
       const questionBlocks = document.querySelectorAll('.question-block');
const errors = [];

if (questionBlocks.length === 0) {
    e.preventDefault();
    alert('Додайте хоча б одне питання перед збереженням.');
    return;
}

        questionBlocks.forEach((block, index) => {
            const correctInput = block.querySelector(`input[name="q[${index}][correct]"]`);
            const correctValue = parseInt(correctInput.value, 10);

            const answerInputs = block.querySelectorAll(`input[name="q[${index}][answers][]"]`);
            const answerCount = answerInputs.length;

            // Валідація: чи правильний номер не більший за кількість відповідей
            if (correctValue > answerCount) {
                errors.push(`Питання №${index + 1}: правильний варіант №${correctValue} перевищує кількість варіантів (${answerCount}).`);
                correctInput.classList.add('border-red-500', 'ring-red-500');
            } else {
                correctInput.classList.remove('border-red-500', 'ring-red-500');
            }
               // Валідація: мінімум 3 варіанти відповіді
        if (answerCount < 3) {
            errors.push(`Питання №${index + 1}: має бути мінімум 3 варіанти відповіді.`);
            block.querySelector('.answers').classList.add('border-red-500');
        } else {
            block.querySelector('.answers').classList.remove('border-red-500');
        }


            const answerInput = answerInputs[correctValue - 1];
            if (!answerInput || !answerInput.value.trim()) {
                errors.push(`Питання №${index + 1}: правильний варіант №${correctValue} порожній.`);
                answerInput?.classList.add('border-red-500', 'ring-red-500');
            } else {
                answerInput?.classList.remove('border-red-500', 'ring-red-500');
            }
        });

        if (errors.length > 0) {
            e.preventDefault();
            alert('Будь ласка, виправте помилки перед збереженням:\n\n' + errors.join('\n'));
        }
    });

    function addAnswer(button, index) {
        const container = button.closest('.answers');
        const currentInputs = container.querySelectorAll('input[type="text"]');
        
        if (currentInputs.length >= 26) {
            alert('Максимальна кількість варіантів відповіді — 26 (від A до Z).');
            return;
        }

        const label = document.createElement('label');
        label.className = 'block font-medium text-sm mt-2';
        label.textContent = `Варіант ${currentInputs.length + 1}`;

        const input = document.createElement('input');
        input.type = 'text';
        input.name = `q[${index}][answers][]`;
        input.required = true;
        input.className = 'mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-700 focus:ring focus:ring-indigo-200';

        const removeButton = document.createElement('button');
        removeButton.type = 'button';
        removeButton.className = 'text-red-500 hover:text-red-700 remove-answer';
        removeButton.textContent = '×';

        removeButton.addEventListener('click', function () {
            const answerItem = label.closest('.answer-item');
            answerItem.remove();
            updateAnswerLabels(container);
        });

        const answerItem = document.createElement('div');
        answerItem.className = 'answer-item flex items-center gap-2 mt-2';
        answerItem.appendChild(label);
        answerItem.appendChild(input);
        answerItem.appendChild(removeButton);

        container.insertBefore(answerItem, button);

        // Оновити max для input правильного варіанту
        const correctInput = container.parentElement.querySelector(`input[name="q[${index}][correct]"]`);
        correctInput.max = currentInputs.length + 1;
    }

   function updateAnswerLabels(container) {
    const answerItems = container.querySelectorAll('.answer-item');
    answerItems.forEach((item, index) => {
        const label = item.querySelector('label');
        label.textContent = `Варіант ${index + 1}`;
    });
}
document.querySelectorAll('.remove-answer').forEach(button => {
    button.addEventListener('click', function () {
        const answerItem = button.closest('.answer-item');
        answerItem.remove();
    });
});

document.querySelectorAll('.remove-tag').forEach(button => {
    button.addEventListener('click', function () {
        const tagItem = button.closest('.flex');
        tagItem.remove();
    });
});

document.getElementById('addTagBtn').addEventListener('click', function () {
    const selectedTag = document.getElementById('tagSelect').value;
    if (selectedTag) {
        const tagText = document.querySelector(`#tagSelect option[value="${selectedTag}"]`).textContent;
        const tagItem = document.createElement('div');
        tagItem.className = 'flex items-center bg-indigo-100 dark:bg-indigo-700 text-sm px-2 py-1 rounded';
        tagItem.textContent = tagText;

        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'tags[]';
        input.value = selectedTag;

        const removeButton = document.createElement('button');
        removeButton.type = 'button';
        removeButton.className = 'ml-2 text-red-500 remove-tag';
        removeButton.textContent = '×';

        removeButton.addEventListener('click', function () {
            tagItem.remove();
        });

        tagItem.appendChild(input);
        tagItem.appendChild(removeButton);

        document.getElementById('selectedTags').appendChild(tagItem);
    }
});
document.addEventListener('click', function (e) {
    if (e.target.classList.contains('remove-question')) {
        const block = e.target.closest('.question-block');
        block.remove();
        renumberQuestions();
    }
});

function renumberQuestions() {
    const blocks = document.querySelectorAll('.question-block');
    blocks.forEach((block, index) => {
        // Перенумерація імен
        block.querySelector('input[name^="q["]').name = `q[${index}][text]`;
        block.querySelectorAll('input[name^="q["]').forEach((input, i) => {
            if (input.name.includes('[answers]')) {
                input.name = `q[${index}][answers][]`;
            } else if (input.name.includes('[correct]')) {
                input.name = `q[${index}][correct]`;
            }
        });

        // Перенумерація кнопки додавання варіанту
        const addBtn = block.querySelector('button[onclick^="addAnswer"]');
        if (addBtn) {
            addBtn.setAttribute('onclick', `addAnswer(this, ${index})`);
        }

        // Оновити всі мітки "Варіант X"
        const answersContainer = block.querySelector('.answers');
        if (answersContainer) updateAnswerLabels(answersContainer);
    });
}

</script>

</x-app-layout> 