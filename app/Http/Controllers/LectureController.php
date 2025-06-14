<?php

namespace App\Http\Controllers;

use App\Models\Lecture;
use App\Models\Module;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\IOFactory;
 use PhpOffice\PhpWord\Writer\HTML;


class LectureController extends Controller
{
       public function create(Module $module)
    {
        $tags = \App\Models\Tag::all();
return view('lectures.create', compact('module', 'tags'));

    }

  public function store(Request $request, Module $module)
{
    $data = $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'content' => 'nullable|string',
        'content_file' => 'nullable|file|mimes:txt,docx',
        'video' => 'nullable|file|mimes:mp4,webm,ogg',
        'video_url' => 'nullable|url',
        'content_type' => 'required|in:text,file',
    ]);

    // Обробка контенту
    if ($request->input('content_type') === 'text' && $request->filled('content')) {
        $filename = 'lectures/' . uniqid() . '.txt';
        Storage::disk('public')->put($filename, $request->input('content'));
        $data['file_url'] = $filename;
    } elseif ($request->input('content_type') === 'file' && $request->hasFile('content_file')) {
        $data['file_url'] = $request->file('content_file')->store('lectures', 'public');
    }

    // Обробка відео (виправлена версія без дублювання)
    if ($request->filled('video_url')) {
        $data['video_url'] = $request->input('video_url');
    } elseif ($request->hasFile('video')) {
        $data['video_url'] = $request->file('video')->store('lectures', 'public');
    }

    $data['module_id'] = $module->id;

    // Встановлення порядкового номера лекції
    $data['order'] = Lecture::where('module_id', $module->id)->max('order') + 1;

    $lecture = Lecture::create($data);
    $lecture->tags()->sync($request->input('tags', []));

    return redirect()->route('theory.index')->with('success', 'Лекцію додано');
}





   public function show(Lecture $lecture)
{
 $nextLecture = Lecture::where('module_id', $lecture->module_id)
        ->where('order', '>', $lecture->order)
        ->orderBy('order')
        ->first();

    // Отримуємо всі теги лекції
    $tagIds = $lecture->tags->pluck('id');

    // Шукаємо перший тест, який має хоча б один із тегів лекції
    $test = \App\Models\Test::whereHas('tags', function ($query) use ($tagIds) {
        $query->whereIn('tags.id', $tagIds);
    })->first();

    return view('theory.show', compact('lecture', 'nextLecture', 'test'));
}




    public function destroy(Lecture $lecture)
{
    $lecture->delete();
    return redirect()->back()->with('success', 'Лекцію видалено');
}

public function stream(Lecture $lecture)
{
      // Перевірка наявності відео
    if (empty($lecture->video_url)) {
        abort(404, 'Відео не знайдено');
    }

    // Обробка посилань на Google Drive
    if (filter_var($lecture->video_url, FILTER_VALIDATE_URL)) {
        $driveUrl = $this->getEmbeddableDriveLink($lecture->video_url);
        return redirect()->away($driveUrl);
    }


    // Обробка локальних файлів
    $path = storage_path('app/public/' . $lecture->video_url);

    if (!file_exists($path)) {
        abort(404, 'Файл відео не знайдено');
    }

    $mime = mime_content_type($path);
    $size = filesize($path);
    $start = 0;
    $end = $size - 1;

    header("Content-Type: $mime");
    header("Accept-Ranges: bytes");

    if (isset($_SERVER['HTTP_RANGE'])) {
        $range = $_SERVER['HTTP_RANGE'];
        $range = str_replace('bytes=', '', $range);
        [$start, $endRange] = explode('-', $range);
        $start = intval($start);
        if ($endRange !== '') {
            $end = intval($endRange);
        }

        http_response_code(206);
    } else {
        http_response_code(200);
    }

    $length = $end - $start + 1;

    header("Content-Length: $length");
    header("Content-Range: bytes $start-$end/$size");

    $file = fopen($path, 'rb');
    fseek($file, $start);

    $buffer = 1024 * 8;
    while (!feof($file) && ($p = ftell($file)) <= $end) {
        if ($p + $buffer > $end) {
            $buffer = $end - $p + 1;
        }
        echo fread($file, $buffer);
        flush();
    }

    fclose($file);
    exit;
}

protected function normalizeGoogleDriveUrl($url)
{
    // Якщо це вже пряме посилання на перегляд
    if (str_contains($url, '/preview') || str_contains($url, 'uc?')) {
        return $url;
    }

    // Витягуємо ID файлу з різних форматів посилань
    if (preg_match('/\/file\/d\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
        $fileId = $matches[1];
        return "https://drive.google.com/file/d/{$fileId}/preview";
    }

    // Для посилань виду drive.google.com/open?id=FILE_ID
    if (str_contains($url, 'open?id=')) {
        $fileId = explode('open?id=', $url)[1];
        return "https://drive.google.com/file/d/{$fileId}/preview";
    }

    return $url;
}

// Додайте цей метод в кінець класу LectureController, але перед закриваючою дужкою класу }
protected function getEmbeddableDriveLink($url)
{
    // Якщо це вже посилання для перегляду
    if (str_contains($url, '/preview')) {
        return $url;
    }

    // Витягуємо ID файлу з різних форматів посилань Google Drive
    $patterns = [
        '/\/file\/d\/([a-zA-Z0-9_-]+)/',       // Стандартний формат
        '/id=([a-zA-Z0-9_-]+)/',               // Формат з параметром id
        '/open\?id=([a-zA-Z0-9_-]+)/',         // Формат /open?id=
        '/drive\/folders\/([a-zA-Z0-9_-]+)/',  // Для папок
    ];

    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $url, $matches)) {
            $fileId = $matches[1];
            
            // Визначаємо тип контенту за розширенням (якщо є)
            $extension = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION);
            
            // Формуємо посилання в залежності від типу контенту
            if (in_array($extension, ['mp4', 'webm', 'avi', 'mov'])) {
                return "https://drive.google.com/file/d/{$fileId}/preview";
            } elseif (in_array($extension, ['doc', 'docx', 'xls', 'xlsx'])) {
                return "https://docs.google.com/document/d/{$fileId}/preview";
            } else {
                return "https://drive.google.com/file/d/{$fileId}/preview";
            }
        }
    }

    // Якщо не вдалося розпізнати формат - повертаємо оригінальне посилання
    return $url;
}

public function edit(Lecture $lecture)
{
   $tags = \App\Models\Tag::all();
return view('theory.edit', compact('lecture', 'tags'));
}


public function update(Request $request, Lecture $lecture)
{
    $data = $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'content_text' => 'nullable|string',
        'content_file' => 'nullable|file|mimes:txt,docx',
        'video' => 'nullable|file|mimes:mp4,webm,ogg',
        'video_url' => 'nullable|url', // Додаємо нове поле
    ]);

    // Обробка контенту (залишаємо як є)
    if ($request->filled('content_text') && $lecture->file_url) {
        $filePath = storage_path('app/public/' . $lecture->file_url);
        $ext = pathinfo($filePath, PATHINFO_EXTENSION);
        if ($ext === 'txt' && file_exists($filePath)) {
            file_put_contents($filePath, $request->input('content_text'));
        }
    }

    if ($request->hasFile('content_file')) {
        if ($lecture->file_url) {
            Storage::disk('public')->delete($lecture->file_url);
        }
        $data['file_url'] = $request->file('content_file')->store('lectures', 'public');
    }

    // Ось сюди додаємо нову логіку для відео
    if ($request->filled('video_url')) {
        // Якщо було локальне відео - видаляємо його
        if ($lecture->video_url && !filter_var($lecture->video_url, FILTER_VALIDATE_URL)) {
            Storage::disk('public')->delete($lecture->video_url);
        }
        $data['video_url'] = $request->input('video_url');
    } elseif ($request->hasFile('video')) {
        // Видаляємо старе відео (якщо воно було не посиланням)
        if ($lecture->video_url && !filter_var($lecture->video_url, FILTER_VALIDATE_URL)) {
            Storage::disk('public')->delete($lecture->video_url);
        }
        $data['video_url'] = $request->file('video')->store('lectures', 'public');
    }

    $lecture->update($data);
    $lecture->tags()->sync($request->input('tags', []));

    return redirect()->route('theory.show', $lecture)->with('success', 'Лекцію оновлено');
}




public function move(Request $request, Lecture $lecture)
{
    $direction = $request->input('direction');

    $swapLecture = Lecture::where('module_id', $lecture->module_id)
        ->where('order', $direction === 'up' ? '<' : '>', $lecture->order)
        ->orderBy('order', $direction === 'up' ? 'desc' : 'asc')
        ->first();

    if ($swapLecture) {
        [$lecture->order, $swapLecture->order] = [$swapLecture->order, $lecture->order];
        $lecture->save();
        $swapLecture->save();
    }

    return back();
}
public function detachTag(Lecture $lecture, Tag $tag)
{
    $lecture->tags()->detach($tag->id);
    return back()->with('success', 'Тег видалено з лекції.');
}
public function attach(Request $request, Lecture $lecture)
{
   $tagId = $request->input('tag_id');

    if (!$lecture->tags()->where('tag_id', $tagId)->exists()) {
        $lecture->tags()->attach($tagId);
    }

    return back();
    
}


}
