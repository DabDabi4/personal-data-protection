<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\Test;
use Illuminate\Http\Request;

class TagController extends Controller
{
     public function index()
    {
        $tags = Tag::all();
        return view('tags.index', compact('tags'));
    }
  public function update(Request $request, Tag $tag)
{
    // Валідація вводу, щоб ім'я було унікальним, окрім поточного тегу
    $request->validate([
        'name' => 'required|string|max:255|unique:tags,name,' . $tag->id,
    ]);

    // Оновлення тегу
    $tag->update(['name' => $request->name]);

    return redirect()->route('tags.index')->with('success', 'Назву тегу оновлено!');
}

public function destroy(Tag $tag)
{
    // Видалити тег
    $tag->delete();

    // Перенаправити з повідомленням про успіх
    return redirect()->route('tags.index')->with('success', 'Тег успішно видалено!');
}
public function store(Request $request)
{
    // Валідація вводу для нового тегу
    $request->validate([
        'name' => 'required|string|max:255|unique:tags,name',
    ]);

    // Створення нового тегу
    Tag::create(['name' => $request->name]);

    // Перенаправлення з повідомленням про успіх
    return redirect()->route('tags.index')->with('success', 'Новий тег успішно створено!');
}

}
