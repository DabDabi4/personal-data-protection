<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TestResult;
use App\Models\Test; // Додайте цей рядок для імпорту моделі Test
use App\Models\Tag;


class TestsController extends Controller
{
    public function destroy($id)
    {
        // Знаходимо тест за id
        $test = Test::findOrFail($id);
    
        // Видаляємо всі результати, пов'язані з тестом
        $test->results()->delete();
    
        // Потім видаляємо сам тест
        $test->delete();
    
        // Переадресовуємо на список тестів з повідомленням про успіх
        return redirect()->route('tests.index')->with('success', 'Тест успішно видалено!');
    }
    public function index(Request $request)
{
    $query = Test::with('tags');

    if ($request->filled('search')) {
        $query->where('title', 'like', '%' . $request->search . '%');
    }

    if ($request->filled('tag')) {
        $query->whereHas('tags', function ($q) use ($request) {
            $q->where('id', $request->tag);
        });
    }

    $tests = $query->get();
    $allTags = Tag::all(); // Для фільтрації по тегах

    return view('tests.index', compact('tests', 'allTags'));
}
public function create()
{
      $allTags = Tag::all();  // Retrieve all tags
    return view('tests.create', compact('allTags')); // Pass the tags to the view
}

public function store(Request $request)
{
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'q' => 'required|array|min:1',
        'q.*.text' => 'required|string',
        'q.*.answers' => 'required|array|min:3', // мінімум 3 відповіді
        'q.*.answers.*' => 'required|string',
        'q.*.correct' => 'required|integer|min:0', // індекс правильної відповіді
        'tags' => 'nullable|array',
        'tags.*' => 'exists:tags,id',

    ]);

    $questions = $validated['q'];
    $content = '';
    $answers = [];

    foreach ($questions as $index => $item) {
        $content .= "{$item['text']}\n";

        foreach ($item['answers'] as $answer) {
            $content .= "{$answer}\n";
        }

       $correctIndex = $item['correct'] - 1;
$correctAnswer = $item['answers'][$correctIndex] ?? '???';
$answers[] = ($index + 1) . ') ' . ($correctIndex + 1) . '. ' . $correctAnswer;


        $content .= "*\n";
    }

    $content .= "_______________\n" . implode("\n", $answers);

    $filename = 'test_' . time() . '.txt';
    \Storage::disk('public')->put("tests/{$filename}", $content);

    $test = Test::create([
        'title' => $validated['title'],
        'description' => $validated['description'] ?? null,
        'file_url' => 'tests/' . $filename,
    ]);

    $test->tags()->sync($validated['tags'] ?? []);


    return response()->json(['success' => 'Тест успішно створено!']);
}

public function show($id)
{
    $test = Test::findOrFail($id);
    $questions = [];

    if ($test->file_url) {
        $filePath = storage_path('app/public/' . $test->file_url);

        if (file_exists($filePath)) {
            $raw = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $questionsRaw = [];
            $answerLines = [];
            $parsingAnswer = false;
            $currentQuestion = [];

            foreach ($raw as $line) {
                $trimmedLine = trim($line);

                if ($trimmedLine === '_______________') {
                    $parsingAnswer = true;
                    continue;
                }

                if ($parsingAnswer) {
                    $answerLines[] = $trimmedLine;
                } else {
                    if ($trimmedLine === '*') {
                        if (!empty($currentQuestion)) {
                            $questionsRaw[] = $currentQuestion;
                            $currentQuestion = [];
                        }
                        
                    } else {
                        $currentQuestion[] = $trimmedLine;
                    }
                }
            }

            if (!$parsingAnswer && !empty($currentQuestion)) {
                $questionsRaw[] = $currentQuestion;
            }

            // Мапа правильних відповідей
            $answersMap = [];
            foreach ($answerLines as $answerLine) {
                if (preg_match('/(\d+)\)\s*(\d+)\.\s*(.+)/u', $answerLine, $matches)) {
                    $qIndex = (int)$matches[1] - 1;
                    $correctIndex = (int)$matches[2] - 1;
                    $correctText = trim($matches[3]);
                    $answersMap[$qIndex] = [
                        'index' => $correctIndex,
                        'text' => $correctText,
                    ];
                }
            }

            // Побудова питань
            foreach ($questionsRaw as $index => $q) {
                $questionText = array_shift($q); // перший рядок — текст питання
                $answers = [];

                foreach ($q as $i => $ans) {
                    // Формуємо мітки A, B, C... або просто 1, 2, 3...
                    $label = chr(65 + $i); // A = 65
                    $answers[$label] = $ans;
                }

                $correctData = $answersMap[$index] ?? ['index' => 0, 'text' => ''];
                $correctLabel = chr(65 + $correctData['index']); // перетворюємо індекс на A, B, C...

                $questions[] = [
                    'question' => $questionText,
                    'answers' => $answers,
                    'correct' => $correctLabel,
                ];
            }
        }
    }

    // Генерація списку правильних відповідей для сесії
    $corrects = [];
    foreach ($questions as $q) {
        $correctLabel = $q['correct'];
        $correctText = $q['answers'][$correctLabel] ?? '';
        $letterToIndex = [];
for ($i = 0; $i < 26; $i++) {
    $letter = chr(65 + $i); // A-Z
    $letterToIndex[$letter] = $i + 1;
}
$corrects[] = ($letterToIndex[$correctLabel] ?? '?') . '. ' . $correctText;

    }
    session(['correct_answers' => $corrects]);

    return view('tests.show', compact('test', 'questions'));
}

public function checkAnswers(Request $request)
{
    $correctAnswers = session('correct_answers', []);
    $score = 0;
    $userAnswers = [];

    
    $userId = auth()->id();
    $testId = $request->input('test_id');

    // Перевірка кількості проходжень
    $attempts = TestResult::where('user_id', $userId)
        ->where('test_id', $testId)
        ->count();

    if ($attempts >= 3) {
        return redirect()->route('tests.show', $testId)
            ->with('error', 'Ви вже пройшли цей тест 3 рази.');
    }
    // Динамічне створення мапи літер до чисел
    $letterToIndex = [];
    for ($i = 0; $i < 26; $i++) {
        $letter = chr(65 + $i); // A, B, C...
        $letterToIndex[$letter] = (string)($i + 1); // A => 1, B => 2, ...
    }

    foreach ($correctAnswers as $i => $correctFullText) {
        $userChoice = $request->input('q' . $i);
        $userAnswers[$i] = $userChoice;

        // Витягуємо номер правильної відповіді (наприклад "3. Текст")
        preg_match('/(\d+)\.\s*(.*)/', $correctFullText, $matches);
       $correctIndex = isset($matches[1]) ? (int)$matches[1] : 1;


        // Порівнюємо
        if ($userChoice && isset($letterToIndex[$userChoice]) && ((int)$letterToIndex[$userChoice]) === $correctIndex) {
            $score++;
        }
    }

    // Зберігаємо результат
    TestResult::create([
        'user_id' => auth()->id(),
        'test_id' => $request->input('test_id'),
        'score' => $score,
        'user_answers' => $userAnswers, // зберігається як JSON
    ]);

    return view('tests.test-result', [
        'score' => $score,
        'total' => count($correctAnswers),
    ]);
    
}




public function showResultDetails(TestResult $result)
{
    $userAnswers = is_array($result->user_answers)
        ? $result->user_answers
        : json_decode($result->user_answers, true);

    $test = $result->test;
    $questions = [];

    $filePath = storage_path('app/public/' . $test->file_url);

    if (file_exists($filePath)) {
        $raw = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $questionsRaw = [];
        $answerLines = [];
        $parsingAnswer = false;
        $currentQuestion = [];

        foreach ($raw as $line) {
            $trimmed = trim($line);

            if ($trimmed === '_______________') {
                $parsingAnswer = true;
                continue;
            }

            if ($parsingAnswer) {
                $answerLines[] = $trimmed;
            } else {
                if ($trimmed === '*') {
                    if (!empty($currentQuestion)) {
                        $questionsRaw[] = $currentQuestion;
                        $currentQuestion = [];
                    }
                } else {
                    $currentQuestion[] = $trimmed;
                }
            }
        }

        if (!$parsingAnswer && !empty($currentQuestion)) {
            $questionsRaw[] = $currentQuestion;
        }

        // Парсимо відповіді
        $answersMap = [];
        foreach ($answerLines as $answerLine) {
            if (preg_match('/(\d+)\)\s*(\d+)\.(.+)/u', $answerLine, $matches)) {
                $answersMap[(int)$matches[1] - 1] = trim($matches[2] . '.' . $matches[3]);
            }
        }

        foreach ($questionsRaw as $index => $q) {
            $questionText = $q[0];
            $answerTexts = array_slice($q, 1);

            // Динамічна генерація міток: A, B, C, D...
            $labels = range('A', chr(65 + count($answerTexts) - 1));

          $answers = collect($answerTexts)
    ->filter()
    ->values()
    ->mapWithKeys(function ($text, $i) use ($labels) {
        return [$labels[$i] => $text];
    })->toArray();


            $correctFull = $answersMap[$index] ?? '';
            preg_match('/(\d+)\.(.*)/', $correctFull, $matches);
            $correctIndex = isset($matches[1]) ? (int)$matches[1] - 1 : 0;
            $correctLabel = $labels[$correctIndex] ?? 'A';

            $questions[] = [
                'question' => $questionText,
                'answers' => $answers,
                'correct' => $correctLabel,
                'user_answer' => $userAnswers[$index] ?? '',
            ];
        }
    }

    return view('tests.result-details', compact('result', 'userAnswers', 'questions'));
}




public function results($testId)
{
    $test = Test::findOrFail($testId);

    $results = $test->results()
        ->where('user_id', auth()->id())
        ->orderByDesc('created_at')
        ->get();

    return view('tests.results', compact('test', 'results'));
}

public function certificateProgress()
{
     $totalScore = TestResult::where('user_id', auth()->id())->sum('score');
    $threshold = 5;

    $progress = round(($totalScore / $threshold) * 100);
    
    return [
        'score' => $totalScore,
        'progress' => min(100, $progress),
        'achieved' => $progress >= 70
    ];
}



public function edit($id)
{
    $test = Test::with('tags')->findOrFail($id);
    $questions = [];

    if ($test->file_url) {
        $filePath = storage_path('app/public/' . $test->file_url);

        if (file_exists($filePath)) {
            $raw = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $questionsRaw = [];
            $answerLines = [];
            $parsingAnswer = false;
            $currentQuestion = [];

            foreach ($raw as $line) {
                $trimmedLine = trim($line);

                if ($trimmedLine === '_______________') {
                    $parsingAnswer = true;
                    continue;
                }

                if ($parsingAnswer) {
                    $answerLines[] = $trimmedLine;
                } else {
                    if ($trimmedLine === '*') {
                        if (!empty($currentQuestion)) {
                            $questionsRaw[] = $currentQuestion;
                            $currentQuestion = [];
                        }
                    } else {
                        $currentQuestion[] = $trimmedLine;
                    }
                }
            }

            if (!empty($currentQuestion)) {
                $questionsRaw[] = $currentQuestion;
            }

            // Парсимо відповіді
            $answersMap = [];
            foreach ($answerLines as $answerLine) {
                if (preg_match('/(\d+)\)\s*(\d+)\.(.+)/u', $answerLine, $matches)) {

                    $answersMap[(int)$matches[1] - 1] = trim($matches[2] . '.' . $matches[3]);
                }
            }

            foreach ($questionsRaw as $index => $q) {
                $questionText = $q[0];
                $answerTexts = array_slice($q, 1);

                $answers = [];
                foreach ($answerTexts as $answerText) {
                    $answers[] = $answerText;
                }

                $correctFull = $answersMap[$index] ?? '';
                preg_match('/(\d+)\.(.*)/', $correctFull, $matches);
                $correctIndex = isset($matches[1]) ? (int)$matches[1] : 1;

                $questions[] = [
                    'question' => $questionText,
                    'text' => $questionText,
                    'answers' => $answers,
                    'correctIndex' => $correctIndex,
                ];
            }
        }
    }

    $allTags = Tag::all();
    return view('tests.edit', compact('test', 'questions', 'allTags'));
}



public function update(Request $request, $id)
{
    $test = Test::findOrFail($id);

    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'tags' => 'nullable|array',
        'tags.*' => 'exists:tags,id',
        'q' => 'required|array|min:1',
        'q.*.text' => 'required|string',
        'q.*.answers' => 'required|array|min:3',
        'q.*.answers.*' => 'required|string',
        'q.*.correct' => 'required|integer|min:1',
    ]);

    $questions = $validated['q'];
    $content = '';
    $answers = [];

    foreach ($questions as $index => $item) {
        $content .= "{$item['text']}\n";

        foreach ($item['answers'] as $answer) {
            $content .= "{$answer}\n";
        }

        $correctIndex = $item['correct'] - 1;
        $correctAnswer = $item['answers'][$correctIndex] ?? '???';

        $answers[] = ($index + 1) . ') ' . ($correctIndex + 1) . '. ' . $correctAnswer;
        $content .= "*\n";
    }

    $content .= "_______________\n" . implode("\n", $answers);

    $filename = $test->file_url ?? 'tests/test_' . $test->id . '.txt';
    \Storage::disk('public')->put($filename, $content);

    $test->update([
        'title' => $validated['title'],
        'description' => $validated['description'],
        'file_url' => $filename,
    ]);

    $test->tags()->sync($validated['tags'] ?? []);

    // Оновлення результатів тесту для користувачів
    $results = $test->results()->get();
    foreach ($results as $result) {
        $userAnswers = is_array($result->user_answers)
            ? $result->user_answers
            : json_decode($result->user_answers, true);

        $newScore = 0;

        foreach ($questions as $i => $item) {
            $correctIndex = $item['correct'];
            $userChoice = $userAnswers[$i] ?? null;

            // A=1, B=2, C=3...
            $map = [];
            for ($j = 0; $j < 26; $j++) {
                $map[chr(65 + $j)] = $j + 1;
            }

            if ($userChoice && isset($map[$userChoice]) && $map[$userChoice] == $correctIndex) {
                $newScore++;
            }
        }

        $result->update(['score' => $newScore]);
    }

    return redirect()->route('tests.index')->with('success', 'Тест і результати успішно оновлено!');
}


}