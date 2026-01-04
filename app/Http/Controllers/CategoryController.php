<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    // 🔹 GET: All Categories
    public function index()
    {
        return response()->json([
            'status' => true,
            'data' => Category::latest()->get()
        ]);
    }

    // 🔹 POST: Store Category
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:categories,name',
        ]);

        $category = Category::create($validated);

        return response()->json([
            'status' => true,
            'message' => 'Category created successfully',
            'data' => $category
        ], 201);
    }

    // 🔹 GET: Single Category
    public function show($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'status' => false,
                'message' => 'Category not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $category
        ]);
    }

    // 🔹 PUT: Update Category
    public function update(Request $request, $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'status' => false,
                'message' => 'Category not found'
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|unique:categories,name,' . $category->id,
        ]);

        $category->update($validated);

        return response()->json([
            'status' => true,
            'message' => 'Category updated successfully',
            'data' => $category
        ]);
    }

    // 🔹 DELETE: Remove Category
    public function destroy($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'status' => false,
                'message' => 'Category not found'
            ], 404);
        }

        $category->delete();

        return response()->json([
            'status' => true,
            'message' => 'Category deleted successfully'
        ]);
    }

    // 🔹 POST: Import Categories from CSV
    public function importCsv(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt'
        ]);

        $rows = array_map('str_getcsv', file($request->file('file')));
        $header = array_map('trim', array_shift($rows));

        DB::transaction(function () use ($rows, $header) {

            foreach ($rows as $row) {
                if (count($row) !== count($header)) {
                    continue; // skip invalid row
                }

                $data = array_combine($header, $row);

                if (empty($data['name'])) {
                    continue; // skip if name is empty
                }

                Category::firstOrCreate(
                    ['name' => $data['name']]
                );
            }
        });

        return response()->json([
            'status' => true,
            'message' => 'CSV imported successfully'
        ]);
    }
}