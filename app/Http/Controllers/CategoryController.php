<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    /**
     * Get all categories
     */
    public function index()
    {
        try {
            $categories = Category::orderBy('name')->get();
            return response()->json($categories);

        } catch (\Exception $e) {
            Log::error('Failed to load categories: ' . $e->getMessage());
            
            // Return default categories if database fails
            $defaultCategories = [
                ['id' => 1, 'name' => 'Work'],
                ['id' => 2, 'name' => 'Personal'],
                ['id' => 3, 'name' => 'Ideas'],
                ['id' => 4, 'name' => 'Tasks'],
                ['id' => 5, 'name' => 'General']
            ];
            
            return response()->json($defaultCategories);
        }
    }

    /**
     * Create a new category
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:100|unique:categories,name'
            ]);

            $category = Category::create($validatedData);
            
            return response()->json([
                'message' => 'Category created successfully!',
                'category' => $category
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Invalid category data',
                'details' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Category creation failed: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to create category'], 500);
        }
    }
}