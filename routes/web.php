<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TheoryController;
use App\Http\Controllers\TestsController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\LectureController;
use App\Models\Module;
use App\Http\Controllers\DocxPreviewController;
use App\Http\Controllers\SettingsController;



Route::get('/', function () {
    $modules = [
        [
            'title' => 'Модуль 1',
            'lectures' => [
                ['title' => 'Лекція 1.1', 'description' => 'Опис лекції 1.1'],
                ['title' => 'Лекція 1.2', 'description' => 'Опис лекції 1.2'],
            ]
        ],
        [
            'title' => 'Модуль 2',
            'lectures' => [
                ['title' => 'Лекція 2.1', 'description' => 'Опис лекції 2.1'],
            ]
        ]
    ];

    return view('welcome', compact('modules'));
});



Route::get('/dashboard', function () {
    $modules = [
        [
            'title' => 'Модуль 1',
            'lectures' => [
                ['title' => 'Лекція 1.1', 'description' => 'Опис лекції 1.1'],
                ['title' => 'Лекція 1.2', 'description' => 'Опис лекції 1.2'],
            ]
        ],
    ];

    return view('welcome', compact('modules'));
})->name('dashboard');



Route::get('/theory', [ModuleController::class, 'index'])->name('theory.index');

Route::post('/modules', [ModuleController::class, 'store'])->name('modules.store');
Route::delete('/modules/{module}', [ModuleController::class, 'destroy'])->name('modules.destroy');


Route::get('/lectures/create/{module}', [LectureController::class, 'create'])->name('lectures.create');
Route::post('/lectures/{module}', [LectureController::class, 'store'])->name('lectures.store');
Route::get('/lectures/{lecture}', [LectureController::class, 'show'])->name('lectures.show');
Route::delete('/lectures/{lecture}', [LectureController::class, 'destroy'])->name('lectures.destroy');
Route::get('/lectures/{lecture}/stream', [LectureController::class, 'stream'])->name('lectures.stream');
Route::patch('/modules/{module}', [ModuleController::class, 'update'])->name('modules.update');
Route::get('/lectures/{lecture}/edit', [LectureController::class, 'edit'])->name('lectures.edit');
Route::put('/lectures/{lecture}', [LectureController::class, 'update'])->name('lectures.update');
Route::get('/theory/{lecture}', [LectureController::class, 'show'])->name('theory.show');

Route::post('/lectures/{lecture}/move', [LectureController::class, 'move'])->name('lectures.move');
Route::delete('/lectures/{lecture}/tags/{tag}', [LectureController::class, 'detachTag'])->name('lectures.tags.detach');
Route::post('/lectures/{lecture}/tags', [LectureController::class, 'attach'])->name('lectures.tags.attach');

Route::get('/docx/{filename}', [\App\Http\Controllers\DocxPreviewController::class, 'show'])->name('docx.show');
Route::get('/certificate/download', [\App\Http\Controllers\CertificateController::class, 'download'])->name('certificate.download');



Route::post('/settings/threshold', [SettingsController::class, 'updateThreshold'])->name('settings.update.threshold');



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
     // Нові контролери

     Route::get('/tests', [TestsController::class, 'index'])->name('tests.index');
     Route::post('/submit-test', [TestsController::class, 'checkAnswers']);
     Route::delete('/tests/{id}', [TestsController::class, 'destroy'])->name('tests.destroy');
     Route::get('/tests/create', [TestsController::class, 'create'])->name('tests.create');

Route::post('/tests', [TestsController::class, 'store'])->name('tests.store');
// Додати маршрут для перегляду тесту
Route::get('/tests/{id}', [TestsController::class, 'show'])->name('tests.show');
Route::post('/tests/check', [TestsController::class, 'checkAnswers'])->name('tests.check');
Route::get('/tests/{test}/results', [TestsController::class, 'results'])->name('tests.results');
Route::get('/test/result/{result}', [TestsController::class, 'showResultDetails'])->name('tests.result-details');

Route::get('/tests/{id}/edit', [TestsController::class, 'edit'])->name('tests.edit');
Route::put('/tests/{id}', [TestsController::class, 'update'])->name('tests.update');
Route::delete('/admin/users/{user}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');

Route::get('/tags', [TagController::class, 'index'])->name('tags.index');
 Route::put('/tags/{tag}', [TagController::class, 'update'])->name('tags.update');
 Route::resource('tags', TagController::class)->except(['create', 'show']);
 Route::resource('tags', TagController::class)->except(['create', 'show']);

});
require __DIR__.'/auth.php';
