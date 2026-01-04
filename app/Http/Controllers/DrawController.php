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

    // public function drawWithShuffle(Request $request)
    // {
   
        
    // $category = DB::table('categories')
    //         ->where('name', $request->category)
    //         ->first();

    // if (!$category) {
    //     return response()->json([
    //         'error' => 'Invalid category'
    //     ], 400);
    // }
    
    //     return DB::transaction(function () use ($request) {

    //         $winnerSize = $request->groupSize;
    //         $categoryId = $category->id;
    //         $prizeId = (int) $request->prizeId;

    //         // 1️⃣ Get available tickets
    //         $tickets = DB::table('draw_tickets')
    //             ->where('is_winner', false)
    //             ->pluck('id', 'ticket_number') // ticket_number => id
    //             ->toArray();

    //         if ($winnerSize > count($tickets)) {
    //             return response()->json([
    //                 'error' => 'Not enough tickets'
    //             ], 400);
    //         }

    //         // 2️⃣ Shuffle
    //         $ticketNumbers = array_keys($tickets);
    //         shuffle($ticketNumbers);
    //         $winnerNumbers = array_slice($ticketNumbers, 0, $winnerSize);

    //         // 3️⃣ Create draw
    //         $drawId = DB::table('draws')->insertGetId([
    //             'category_id' => $categoryId,
    //             'prize_id' => $prizeId ,
    //             'draw_size' => $winnerSize,
    //             'created_at' => now(),
    //             'updated_at' => now(),
    //         ]);

    //         // 4️⃣ Save winners
    //         foreach ($winnerNumbers as $ticketNumber) {

    //             $ticketId = $tickets[$ticketNumber];

    //             // mark ticket as winner
    //             DB::table('draw_tickets')
    //                 ->where('id', $ticketId)
    //                 ->update(['is_winner' => true]);

    //             // insert into draw_winners
    //             DB::table('draw_winners')->insert([
    //                 'draw_id' => $drawId,
    //                 'draw_ticket_id' => $ticketId,
    //                 'created_at' => now(),
    //                 'updated_at' => now(),
    //             ]);
    //         }

    //         // 5️⃣ Mark prize as drawn
    //         DB::table('prizes')
    //             ->where('id', $prizeId )
    //             ->update(['is_drawn' => true]);

    //         return response()->json([
    //             'message' => 'Draw completed successfully',
    //             'draw_id' => $drawId,
    //             'winners' => $winnerNumbers,
    //         ]);
    //     });
    // }


//     public function drawWithShuffle(Request $request)
// {
//     $category = DB::table('categories')
//         ->where('name', $request->category)
//         ->first();

//     if (!$category) {
//         return response()->json([
//             'error' => 'Invalid category'
//         ], 400);
//     }

//     return $request->all();
//     return DB::transaction(function () use ($request, $category) {

//         $winnerSize = $request->groupSize;
//         $categoryId = $category->id;
//         $prizeId = (int) $request->prizeId;

//         // 1️⃣ Get available tickets
//         $tickets = DB::table('draw_tickets')
//             ->where('is_winner', false)
//             ->pluck('id', 'ticket_number')
//             ->toArray();

//         if ($winnerSize > count($tickets)) {
//             return response()->json([
//                 'error' => 'Not enough tickets'
//             ], 400);
//         }

//         // 2️⃣ Shuffle
//         $ticketNumbers = array_keys($tickets);
//         shuffle($ticketNumbers);
//         $winnerNumbers = array_slice($ticketNumbers, 0, $winnerSize);

//         // 3️⃣ Create draw
//         $drawId = DB::table('draws')->insertGetId([
//             'category_id' => $categoryId,
//             'prize_id' => $prizeId,
//             'draw_size' => $winnerSize,
//             'created_at' => now(),
//             'updated_at' => now(),
//         ]);

//         // 4️⃣ Save winners
//         foreach ($winnerNumbers as $ticketNumber) {
//             $ticketId = $tickets[$ticketNumber];

//             DB::table('draw_tickets')
//                 ->where('id', $ticketId)
//                 ->update(['is_winner' => true]);

//             DB::table('draw_winners')->insert([
//                 'draw_id' => $drawId,
//                 'draw_ticket_id' => $ticketId,
//                 'created_at' => now(),
//                 'updated_at' => now(),
//             ]);
//         }

//         // 5️⃣ Mark prize as drawn
//         DB::table('prizes')
//             ->where('id', $prizeId)
//             ->update(['is_drawn' => true]);


//             $results = DB::table('draw_winners')
//     ->join('draw_tickets', 'draw_winners.draw_ticket_id', '=', 'draw_tickets.id')
//     ->join('draws', 'draw_winners.draw_id', '=', 'draws.id')
//     ->join('prizes', 'draws.prize_id', '=', 'prizes.id')
//     ->join('categories', 'draws.category_id', '=', 'categories.id')
//     ->where('draw_winners.draw_id', $drawId)
//     ->select([
//         'draw_tickets.ticket_number',
//         'prizes.name as prize',
//         'categories.name as category',
//     ])
//     ->get();


//         return response()->json([
//             'message' => 'Draw completed successfully',
//             'winners' => $results,
//         ]);
//     });
// }

public function drawWithShuffle(Request $request)
{
    $request->validate([
        'category'  => 'required|string',
        'groupSize' => 'required|integer|min:1',
        'prizeId'   => 'nullable', // can be null | single | array
    ]);

    $category = DB::table('categories')
        ->where('name', $request->category)
        ->first();

    if (!$category) {
        return response()->json([
            'error' => 'Invalid category'
        ], 400);
    }

    $totalWinnerSize = (int) $request->groupSize;
    $categoryId = $category->id;

    return DB::transaction(function () use ($request, $totalWinnerSize, $categoryId) {

        /* -------------------------
           Resolve Prize IDs
        --------------------------*/

        if (empty($request->prizeId)) {

            // prizeId not sent → get all undrawn prizes of category
            $prizeIds = DB::table('prizes')
                ->where('category_id', $categoryId)
                ->where('is_drawn', false)
                ->pluck('id')
                ->toArray();

            if (empty($prizeIds)) {
                return response()->json([
                    'error' => 'No available prizes for this category'
                ], 400);
            }

        } else {
            // prizeId can be single or array
            $prizeIds = is_array($request->prizeId)
                ? array_map('intval', $request->prizeId)
                : [(int) $request->prizeId];
        }

        $prizeCount = count($prizeIds);

        /* -------------------------
           Calculate per-prize winner distribution
        --------------------------*/
        
        // Distribute totalWinnerSize evenly across prizes
        $perPrizeWinners = intval(floor($totalWinnerSize / $prizeCount));
        $remainderWinners = $totalWinnerSize % $prizeCount;
        
        // Create array of winner counts per prize
        $winnerCounts = [];
        foreach ($prizeIds as $index => $prizeId) {
            $winnerCounts[$prizeId] = $perPrizeWinners;
            // Distribute remainder winners to first few prizes
            if ($index < $remainderWinners) {
                $winnerCounts[$prizeId]++;
            }
        }

        /* -------------------------
           Validate sufficient tickets
        --------------------------*/

        $totalAvailableTickets = DB::table('draw_tickets')
            ->where('is_winner', false)
            ->count();

        if ($totalWinnerSize > $totalAvailableTickets) {
            return response()->json([
                'error' => "Not enough tickets. Need {$totalWinnerSize} but only {$totalAvailableTickets} available."
            ], 400);
        }

        $allResults = [];
        $selectedTicketIds = []; // Track already selected tickets

        /* -------------------------
           Draw for each prize
        --------------------------*/

        foreach ($prizeIds as $prizeId) {

            $winnersNeededForPrize = $winnerCounts[$prizeId];

            // 1️⃣ get available tickets (excluding already selected ones)
            $tickets = DB::table('draw_tickets')
                ->where('is_winner', false)
                ->whereNotIn('id', $selectedTicketIds)
                ->pluck('id', 'ticket_number')
                ->toArray();

            if ($winnersNeededForPrize > count($tickets)) {
                return response()->json([
                    'error' => "Not enough remaining tickets for prize {$prizeId}"
                ], 400);
            }

            // 2️⃣ shuffle and select tickets for this prize
            $ticketNumbers = array_keys($tickets);
            shuffle($ticketNumbers);
            $winnerNumbersForPrize = array_slice($ticketNumbers, 0, $winnersNeededForPrize);

            // 3️⃣ create draw
            $drawId = DB::table('draws')->insertGetId([
                'category_id' => $categoryId,
                'prize_id'    => $prizeId,
                'draw_size'   => $winnersNeededForPrize,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);

            // 4️⃣ save winners
            foreach ($winnerNumbersForPrize as $ticketNumber) {
                $ticketId = $tickets[$ticketNumber];
                $selectedTicketIds[] = $ticketId; // Mark as selected

                DB::table('draw_tickets')
                    ->where('id', $ticketId)
                    ->update(['is_winner' => true]);

                DB::table('draw_winners')->insert([
                    'draw_id'        => $drawId,
                    'draw_ticket_id' => $ticketId,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);
            }

            // 5️⃣ mark prize as drawn
            DB::table('prizes')
                ->where('id', $prizeId)
                ->update(['is_drawn' => true]);

            // 6️⃣ collect result
            $results = DB::table('draw_winners')
                ->join('draw_tickets', 'draw_winners.draw_ticket_id', '=', 'draw_tickets.id')
                ->join('draws', 'draw_winners.draw_id', '=', 'draws.id')
                ->join('prizes', 'draws.prize_id', '=', 'prizes.id')
                ->join('categories', 'draws.category_id', '=', 'categories.id')
                ->where('draw_winners.draw_id', $drawId)
                ->select([
                    'draw_tickets.ticket_number',
                    'prizes.name as prize',
                    'categories.name as category',
                ])
                ->get();

            $allResults[] = $results;
        }

        return response()->json([
            'message' => 'Draw completed successfully',
            'data'    => $allResults,
            'summary' => [
                'total_winners' => $totalWinnerSize,
                'prize_count' => $prizeCount,
                'winners_per_prize' => $winnerCounts,
            ]
        ]);
    });
}

public function getDrawResults()
{
    $results = DB::table('draw_winners')
                ->join('draw_tickets', 'draw_winners.draw_ticket_id', '=', 'draw_tickets.id')
                ->join('draws', 'draw_winners.draw_id', '=', 'draws.id')
                ->join('prizes', 'draws.prize_id', '=', 'prizes.id')
                ->join('categories', 'draws.category_id', '=', 'categories.id')
                ->select([
                    'draw_tickets.ticket_number',
                    'prizes.name as prize',
                    'categories.name as category',
                    'draws.draw_size',
                    'draws.created_at',
                ])
                ->get();

    return response()->json([
        'message' => 'Draw completed successfully',
        'data'    => $results,
    ]);


}
}
