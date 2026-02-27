<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = \App\Models\Category::where('user_id', \Illuminate\Support\Facades\Auth::id())->get();
        return view('categories.index', compact('categories'));
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        \App\Models\Category::create([
            'user_id' => \Illuminate\Support\Facades\Auth::id(),
            'name' => $validated['name'],
        ]);

        return redirect()->route('categories.index')->with('success', 'カテゴリを作成しました。');
    }

    public function update(\Illuminate\Http\Request $request, \App\Models\Category $category)
    {
        if ($category->user_id !== \Illuminate\Support\Facades\Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category->update($validated);

        return redirect()->route('categories.index')->with('success', 'カテゴリを更新しました。');
    }

    public function destroy(\App\Models\Category $category)
    {
        if ($category->user_id !== \Illuminate\Support\Facades\Auth::id()) {
            abort(403);
        }

        $category->delete();

        return redirect()->route('categories.index')->with('success', 'カテゴリを削除しました。');
    }
}
