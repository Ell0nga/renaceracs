<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExpenseCategory;

class CategoryController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category = ExpenseCategory::create([
            'name' => $validated['name'],
            'description' => '', // o puedes permitir enviarla también si quieres
        ]);

        return response()->json($category);
    }
}
