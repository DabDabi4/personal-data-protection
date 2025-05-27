<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

class AdminUserController extends Controller
{
    public function destroy(User $user): RedirectResponse
{
    if (auth()->user()->role !== 'admin') {
        abort(403);
    }

    if ($user->id === auth()->id()) {
        return redirect()->route('profile.edit')->with('error', 'Ви не можете видалити себе.');
    }

    $user->delete();

    return redirect()->route('profile.edit')->with('success', 'Користувача видалено.');
}

}
