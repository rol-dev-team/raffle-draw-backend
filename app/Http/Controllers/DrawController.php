<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DrawController extends Controller
{
    // public function drawWithShuffle(Request $request)
    // {
    //     $winnerSize = $request->winnersize;

    //     // 1. Get tickets
    //     $tickets = DB::table('tickets')
    //         ->where('catid', $request->cat)
    //         ->where('status', 'active')
    //         ->pluck('ticket_number')
    //         ->toArray();

    //     if ($winnerSize > count($tickets)) {
    //         return response()->json([
    //             'error' => 'Not enough tickets'
    //         ], 400);
    //     }

    //     // 2. Lock tickets
    //     // DB::table('tickets')
    //     //     ->where('catid', $request->cat)
    //     //     ->update(['status' => 'locked']);

    //     // 3. Shuffle once
    //     shuffle($tickets);

    //     // 4. Pick winners
    //     $winners = array_slice($tickets, 0, $winnerSize);

    //     return response()->json([
    //         'message' => 'Draw completed successfully',
    //         'winners' => $winners
    //     ]);
    // }

    public function drawWithShuffle(Request $request)
    {
        $request->validate([
            'cat' => 'required|exists:categories,id',
            'winnersize' => 'required|integer|min:1',
            'prize_id' => 'required|exists:prizes,id',
        ]);

        return DB::transaction(function () use ($request) {

            $winnerSize = $request->winnersize;

            // 1️⃣ Get available tickets
            $tickets = DB::table('draw_tickets')
                ->where('is_winner', false)
                ->pluck('id', 'ticket_number') // ticket_number => id
                ->toArray();

            if ($winnerSize > count($tickets)) {
                return response()->json([
                    'error' => 'Not enough tickets'
                ], 400);
            }

            // 2️⃣ Shuffle
            $ticketNumbers = array_keys($tickets);
            shuffle($ticketNumbers);

            $winnerNumbers = array_slice($ticketNumbers, 0, $winnerSize);

            // 3️⃣ Create draw
            $drawId = DB::table('draws')->insertGetId([
                'category_id' => $request->cat,
                'prize_id' => $request->prize_id,
                'draw_size' => $winnerSize,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 4️⃣ Save winners
            foreach ($winnerNumbers as $ticketNumber) {

                $ticketId = $tickets[$ticketNumber];

                // mark ticket as winner
                DB::table('draw_tickets')
                    ->where('id', $ticketId)
                    ->update(['is_winner' => true]);

                // insert into draw_winners
                DB::table('draw_winners')->insert([
                    'draw_id' => $drawId,
                    'draw_ticket_id' => $ticketId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // 5️⃣ Mark prize as drawn
            DB::table('prizes')
                ->where('id', $request->prize_id)
                ->update(['is_drawn' => true]);

            return response()->json([
                'message' => 'Draw completed successfully',
                'draw_id' => $drawId,
                'winners' => $winnerNumbers,
            ]);
        });
    }

}
