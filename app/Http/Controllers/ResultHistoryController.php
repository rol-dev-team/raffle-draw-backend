<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ResultHistory;
use Carbon\Carbon;

class ResultHistoryController extends Controller
{
    /**
     * GET /result-histories
     * Fetch all result history entries, newest first
     */
    public function index()
    {
        $data = ResultHistory::orderBy('draw_timestamp', 'desc')->get();
        return response()->json(['status' => true, 'data' => $data]);
    }

    /**
     * POST /result-histories
     * Store multiple draw results
     */
    public function store(Request $request)
    {
        $request->validate([
            'results' => 'required|array',
        ]);

        $entries = collect($request->input('results'))->map(function ($item) {
            // Convert ISO 8601 timestamp to MySQL DATETIME
            $drawTimestamp = isset($item['timestamp'])
                ? Carbon::parse($item['timestamp'])->format('Y-m-d H:i:s')
                : now();

            return [
                'ticket_number' => $item['ticketNumber'],
                'prize_id'      => $item['prize']['id'],
                'prize_name'    => $item['prize']['name'],
                'category'      => $item['category'],
                'assigned_to'   => $item['prize']['assignedTo'] ?? null,
                'draw_timestamp'=> $drawTimestamp,
                'created_at'    => now(),
                'updated_at'    => now(),
            ];
        })->toArray();

        ResultHistory::insert($entries);

        return response()->json(['status' => true, 'data' => $entries]);
    }

    /**
     * DELETE /result-histories
     * Optional: truncate all result history entries
     */
    public function destroyAll()
    {
        ResultHistory::truncate();
        return response()->json(['status' => true]);
    }
}
