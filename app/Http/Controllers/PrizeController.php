<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prize;
use Illuminate\Support\Facades\DB;

class PrizeController extends Controller
{
    // 🔹 GET: All Prizes
    public function index()
    {
        return response()->json([
            'status' => true,
            'data' => Prize::all(),
        ]);
    }

    // 🔹 POST: Store Prize
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                function ($attribute, $value, $fail) use ($request) {
                    $exists = Prize::where('name', $value)
                        ->where('category_id', $request->category_id)
                        ->exists();
                    if ($exists) {
                        $fail('Prize with this name already exists in the selected category.');
                    }
                },
            ],
            'category_id' => 'required|exists:categories,id',
            'is_drawn' => 'boolean'
        ]);

        $prize = Prize::create($validated);

        return response()->json([
            'status' => true,
            'message' => 'Prize created successfully',
            'data' => $prize
        ], 201);
    }

    // 🔹 GET: Single Prize
    public function show($id)
    {
        $prize = Prize::with('category')->find($id);

        if (!$prize) {
            return response()->json([
                'status' => false,
                'message' => 'Prize not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $prize
        ]);
    }

    // 🔹 PUT: Update Prize
    public function update(Request $request, $id)
    {
        $prize = Prize::find($id);

        if (!$prize) {
            return response()->json([
                'status' => false,
                'message' => 'Prize not found'
            ], 404);
        }

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                function ($attribute, $value, $fail) use ($request, $prize) {
                    $exists = Prize::where('name', $value)
                        ->where('category_id', $request->category_id)
                        ->where('id', '!=', $prize->id)
                        ->exists();
                    if ($exists) {
                        $fail('Prize with this name already exists in the selected category.');
                    }
                },
            ],
            'category_id' => 'required|exists:categories,id',
            'is_drawn' => 'boolean'
        ]);

        $prize->update($validated);

        return response()->json([
            'status' => true,
            'message' => 'Prize updated successfully',
            'data' => $prize
        ]);
    }

    // 🔹 DELETE: Remove Prize
    public function destroy($id)
    {
        $prize = Prize::find($id);

        if (!$prize) {
            return response()->json([
                'status' => false,
                'message' => 'Prize not found'
            ], 404);
        }

        $prize->delete();

        return response()->json([
            'status' => true,
            'message' => 'Prize deleted successfully'
        ]);
    }

    // 🔹 POST: Import CSV
    public function importCsv(Request $request)
{
    $request->validate([
        'csv' => 'required|mimes:csv,txt'
    ]);

    $rows = array_map('str_getcsv', file($request->file('csv')));
    $header = array_map('trim', array_shift($rows)); // assumes first row is header: Category,Prize

    $categories = DB::table('categories')->pluck('id', 'name')->toArray();

    DB::transaction(function () use ($rows, $header, $categories) {
        foreach ($rows as $row) {
            if (count($row) !== count($header)) continue;

            $data = array_combine($header, $row);

            $categoryName = $data['Category'] ?? null;
            $prizeName    = $data['Prize'] ?? null;

            if (!$categoryName || !$prizeName) continue;
            if (!isset($categories[$categoryName])) continue;

            \App\Models\Prize::firstOrCreate([
                'name' => $prizeName,
                'category_id' => $categories[$categoryName],
            ], [
                'is_drawn' => false,
            ]);
        }
    });

    return response()->json([
        'status' => true,
        'message' => 'Prize CSV imported successfully',
        'imported_count' => count($rows)
    ]);
}
}