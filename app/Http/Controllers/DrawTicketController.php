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
            'data' => DrawTicket::all()
        ]);
    }

    // POST create a single ticket
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ticket_number' => 'required|string|unique:draw_tickets,ticket_number',
        ]);

        $ticket = DrawTicket::create($validated);

        return response()->json([
            'status' => true,
            'data' => $ticket,
            'message' => 'Ticket created successfully'
        ], 201);
    }

    // DELETE ticket
    public function destroy($id)
    {
        $ticket = DrawTicket::find($id);
        if (!$ticket) {
            return response()->json(['status' => false, 'message' => 'Ticket not found'], 404);
        }

        $ticket->delete();

        return response()->json(['status' => true, 'message' => 'Ticket deleted successfully']);
    }

    // POST import tickets CSV
    public function importCsv(Request $request)
    {
        $request->validate([
            'csv' => 'required|mimes:csv,txt'
        ]);

        $rows = array_map('str_getcsv', file($request->file('csv')));
        $header = array_map('trim', array_shift($rows)); // expects header: ticket_number

        DB::transaction(function () use ($rows, $header) {
            foreach ($rows as $row) {
                if (count($row) !== count($header)) continue;

                $data = array_combine($header, $row);
                if (empty($data['ticket_number'])) continue;

                DrawTicket::firstOrCreate([
                    'ticket_number' => $data['ticket_number']
                ]);
            }
        });

        return response()->json([
            'status' => true,
            'message' => 'Tickets imported successfully',
            'imported_count' => count($rows)
        ]);
    }
}
