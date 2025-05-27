<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Створити тест') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form id="createTestForm" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div>
                            <label for="title">Назва тесту</label>
                            <input type="text" name="title" id="title" required class="input w-full text-black">
                        </div>

                        <div class="mt-4">
                            <label for="description">Опис тесту</label>
                            <textarea name="description" id="description" class="input w-full text-black"></textarea>
                        </div>

                        <div class="mt-4">
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
                            <div id="selectedTags" class="mt-2 flex flex-wrap gap-2"></div>
                        </div>

                        <div id="questionsContainer" class="mt-6 space-y-4"></div>

                        <button type="button" onclick="addQuestion()" class="bg-green-500 text-white p-2 rounded mt-2">Додати питання</button>

                        <input type="file" id="fileInput" name="file" style="display:none" />

                        <button type="submit" class="bg-blue-500 text-white p-2 mt-4 rounded">Створити тест</button>
                    </form>

                    <div id="message" class="mt-4"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let questionCount = 0;

        function addQuestion() {
            const container = document.getElementById('questionsContainer');
            const index = questionCount++;

            const html = `
                <div class="p-4 bg-gray-100 dark:bg-gray-700 rounded text-black dark:text-white" id="question-${index}">
                    <label>Питання ${index + 1}</label>
                    <input type="text" name="q[${index}][text]" class="input w-full mt-1 mb-2 text-black">

                    <div class="space-y-1 answer-options" id="answers-${index}">
                        ${generateAnswerInput(index, 0)}
                        ${generateAnswerInput(index, 1)}
                        ${generateAnswerInput(index, 2)}
                    </div>
                    <button type="button" class="mt-2 text-sm text-blue-500" onclick="addAnswerOption(${index})">+ Додати варіант</button>

                    <label class="mt-2 block">Правильна відповідь:</label>
                    <select name="q[${index}][correct]" class="input text-black correct-select">
                        <option value="">Оберіть правильну відповідь</option>
                    </select>
                </div>
            `;

            container.insertAdjacentHTML('beforeend', html);
        }

        function generateAnswerInput(qIndex, optIndex) {
            const letter = String.fromCharCode(65 + optIndex); // A, B, C...
            return `<input type="text" name="q[${qIndex}][answers][${optIndex}]" placeholder="${optIndex + 1}. Варіант ${letter}" class="input w-full text-black" oninput="updateSelect(${qIndex})">`;
        }

       function addAnswerOption(qIndex) {
    const container = document.getElementById(`answers-${qIndex}`);
    const count = container.querySelectorAll('input').length;

    if (count >= 26) {
        alert("Максимум 26 варіантів відповіді (A–Z).");
        return;
    }

    container.insertAdjacentHTML('beforeend', generateAnswerInput(qIndex, count));
    updateSelect(qIndex);
}


        function updateSelect(index) {
            const questionDiv = document.getElementById(`question-${index}`);
            const select = questionDiv.querySelector('.correct-select');
            const inputs = questionDiv.querySelectorAll(`#answers-${index} input`);

            select.innerHTML = '<option value="">Оберіть правильну відповідь</option>';

            inputs.forEach((input, i) => {
                if (input.value.trim() !== '') {
                    const letter = String.fromCharCode(65 + i);
                    select.innerHTML += `<option value="${i + 1}">${i + 1} (${letter})</option>`;
                }
            });
        }

        const form = document.getElementById('createTestForm');
        const messageDiv = document.getElementById('message');

        form.addEventListener('submit', async function (event) {
            event.preventDefault();

            const formData = new FormData(form);
            const questions = [];

            for (let i = 0; i < questionCount; i++) {
                const q = formData.get(`q[${i}][text]`);
                const correct = formData.get(`q[${i}][correct]`);
                const answers = [];

                let j = 0;
                while (true) {
                    const val = formData.get(`q[${i}][answers][${j}]`);
                    if (val === null) break;
                    if (val.trim() !== '') answers.push(val.trim());
                    j++;
                }

                if (!q || answers.length < 3 || !correct || correct > answers.length) continue;

                questions.push({ q, answers, correct });
            }

            if (questions.length === 0) {
                messageDiv.innerHTML = `<p class="text-red-500">Додайте хоча б одне повне питання з мінімум трьома варіантами.</p>`;
                return;
            }

            let content = '';
            let answers = [];

            questions.forEach((item, i) => {
                content += `${item.q}\n`;
                item.answers.forEach(ans => {
                    content += `${ans}\n`;
                });
                const correctText = item.answers[item.correct - 1];
                answers.push(`${i + 1}) ${item.correct}. ${correctText}`);

            });

            content += "_______________\n" + answers.join('\n');

            const file = new File([content], "test.txt", { type: "text/plain" });
            const data = new FormData(form);
            data.set('file', file);

            axios.post("{{ route('tests.store') }}", data)
                .then(res => {
                    messageDiv.innerHTML = `<p class="text-green-500">Тест успішно створено!</p>`;
                    form.reset();
                    document.getElementById('questionsContainer').innerHTML = '';
                    questionCount = 0;
                })
                .catch(err => {
                    messageDiv.innerHTML = `<p class="text-red-500">Помилка: ${err.response?.data?.message || 'невідома помилка'}</p>`;
                });
        });
    </script>

    <script>
        document.getElementById('addTagBtn').addEventListener('click', () => {
            const select = document.getElementById('tagSelect');
            const selectedTagsDiv = document.getElementById('selectedTags');
            const selectedOption = select.options[select.selectedIndex];

            if (!selectedOption.value) return;

            if (Array.from(selectedTagsDiv.children).some(div => div.querySelector('input').value == selectedOption.value)) {
                alert('Цей тег уже додано.');
                return;
            }

            const tagDiv = document.createElement('div');
            tagDiv.className = "flex items-center bg-indigo-100 dark:bg-indigo-700 text-sm px-2 py-1 rounded";
            tagDiv.innerHTML = `
                ${selectedOption.text}
                <input type="hidden" name="tags[]" value="${selectedOption.value}">
                <button type="button" class="ml-2 text-red-500 remove-tag">&times;</button>
            `;

            selectedTagsDiv.appendChild(tagDiv);
        });
    </script>
</x-app-layout>
