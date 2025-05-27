<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\Tag; // ← ДОДАЙ ЦЕ
use Illuminate\Http\Request;

class ModuleController extends Controller
{public function index(Request $request)
{
     $moduleSearch = $request->input('module_search');
    $lectureSearch = $request->input('lecture_search');
    $tagSearch = $request->input('tag_search');

    $modules = Module::with(['lectures.tags']);

    if ($moduleSearch) {
        $modules->where('name', 'like', "%{$moduleSearch}%");
    }

    $modules = $modules->get();

    // Фільтрація лекцій за пошуковим запитом
    if ($lectureSearch || $tagSearch) {
        $modules->transform(function ($module) use ($lectureSearch, $tagSearch) {
            $module->lectures = $module->lectures->filter(function ($lecture) use ($lectureSearch, $tagSearch) {
                $matchesLecture = true;
                $matchesTag = true;

                if ($lectureSearch) {
                    $matchesLecture =
                        stripos($lecture->name, $lectureSearch) !== false ||
                        stripos($lecture->description, $lectureSearch) !== false;
                }

                if ($tagSearch) {
                    $matchesTag = $lecture->tags->contains(function ($tag) use ($tagSearch) {
                        return stripos($tag->name, $tagSearch) !== false;
                    });
                }

                return $matchesLecture && $matchesTag;
            });
            return $module;
        });
    }

    $tags = Tag::all();

    return view('theory.index', compact('modules', 'tags'));
}


    public function create()
    {
        return view('modules.create');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        Module::create($request->only('name'));

       return redirect()->route('theory.index')->with('success', 'Модуль створено');

    }
    public function destroy(Module $module)
{
    $module->delete();
    return redirect()->route('theory.index')->with('success', 'Модуль видалено');
}
public function update(Request $request, Module $module)
{
    $request->validate(['name' => 'required|string|max:255']);
    $module->update(['name' => $request->name]);

    return redirect()->route('theory.index')->with('success', 'Назву модуля оновлено');
}

}
