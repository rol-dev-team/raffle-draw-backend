<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DrawTicket;
use Illuminate\Support\Facades\DB;

class DrawTicketController extends Controller
{
    // GET all tickets
    public function index()
{
    return response()->json([
        'status' => true,
        'data' => DrawTicket::where('is_winner', false)
            ->orderBy('ticket_number', 'asc')
            ->get()
    ]);
}


    // POST create a single ticket
public function store(Request $request)
{
    $request->validate([
        'ticket_number' => 'required|string',
    ]);

    $ticket = DrawTicket::where('ticket_number', $request->ticket_number)->first();

    if ($ticket) {
        return response()->json([
            'status' => false,
            'message' => 'Ticket already exists',
            'data' => $ticket
        ], 409); // Conflict
    }

    $ticket = DrawTicket::create([
        'ticket_number' => $request->ticket_number
    ]);

    return response()->json([
        'status' => true,
        'data' => $ticket,
        'message' => 'Ticket created successfully'
    ], 201);
}


    // DELETE ticket
    public function destroy()
    {
        $ticket = DrawTicket::query()->delete();

        return response()->json(['status' => true, 'message' => 'Ticket deleted successfully']);
    }


    // POST import tickets CSV
//     public function importCsv(Request $request)
// {
//     $request->validate([
//         'file' => 'required|file|mimes:csv,txt'
//     ]);

//     $file = $request->file('file');

//     // CSV read
//     $rows = array_map('str_getcsv', file($file->getRealPath()));

//     if (count($rows) < 2) {
//         return response()->json([
//             'status' => false,
//             'message' => 'CSV file is empty'
//         ], 422);
//     }

//     // Header (ticket_number)
//     $header = array_map('trim', array_shift($rows));

//     $importedCount = 0;

//     DB::transaction(function () use ($rows, $header, &$importedCount) {
//         foreach ($rows as $row) {

//             if (count($row) !== count($header)) {
//                 continue;
//             }

//             $data = array_combine($header, array_map('trim', $row));

//             if (empty($data['ticket_number'])) {
//                 continue;
//             }

//             $ticket = DrawTicket::firstOrCreate([
//                 'ticket_number' => $data['ticket_number']
//             ]);

//             if ($ticket->wasRecentlyCreated) {
//                 $importedCount++;
//             }
//         }
//     });

//     return response()->json([
//         'status' => true,
//         'message' => 'Tickets imported successfully',
//         'imported_count' => $importedCount
//     ]);
// }


public function importCsv(Request $request)
{
    $request->validate([
        'file' => 'required|file|mimes:csv,txt'
    ]);

    $file = $request->file('file');
    $rows = array_map('str_getcsv', file($file->getRealPath()));

    if (count($rows) < 2) {
        return response()->json([
            'status' => false,
            'message' => 'CSV file is empty'
        ], 422);
    }

    $header = array_map('trim', array_shift($rows));

    $inserted = 0;
    $alreadyExists = 0;

    DB::transaction(function () use ($rows, $header, &$inserted, &$alreadyExists) {
        foreach ($rows as $row) {

            if (count($row) !== count($header)) continue;

            $data = array_combine($header, array_map('trim', $row));

            if (empty($data['ticket_number'])) continue;

            $ticket = DrawTicket::firstOrCreate(
                ['ticket_number' => $data['ticket_number']]
            );

            if ($ticket->wasRecentlyCreated) {
                $inserted++;
            } else {
                $alreadyExists++;
            }
        }
    });

    return response()->json([
        'status' => true,
        'message' => $inserted === 0
            ? 'All tickets already exist'
            : 'CSV imported successfully',
        'inserted' => $inserted,
        'already_exists' => $alreadyExists
    ]);
}


}