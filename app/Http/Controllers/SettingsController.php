<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class SettingsController extends Controller
{
   public function updateThreshold(Request $request)
{
    $request->validate([
        'threshold' => 'required|integer|min:1',
    ]);

    $threshold = (int)$request->input('threshold');

    // Підрахунок усіх питань у всіх тестах
    $totalQuestions = 0;
    $tests = \App\Models\Test::all();

    foreach ($tests as $test) {
        if ($test->file_url) {
            $filePath = storage_path('app/public/' . $test->file_url);
            if (file_exists($filePath)) {
                $content = file_get_contents($filePath);
                $totalQuestions += substr_count($content, '*');
            }
        }
    }

    if ($threshold > $totalQuestions) {
        return back()->with('error', 'Поріг не може перевищувати кількість питань у всіх тестах (' . $totalQuestions . ')');
    }

    \App\Models\Setting::set('certificate_threshold', $threshold);

    return back()->with('success', 'Поріг успішно оновлено!');
}

}
