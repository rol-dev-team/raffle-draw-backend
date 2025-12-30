<?php

namespace App\Http\Controllers;

use App\Models\DrawTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DrawTicketController extends Controller
{
    // List all tickets
    public function index()
    {
        $tickets = DrawTicket::all();
        return response()->json($tickets);
    }

    // Store a new ticket
    public function store(Request $request)
    {
        $request->validate([
            'ticket_number' => 'required|string|unique:draw_tickets,ticket_number',
            'is_winner' => 'boolean',
        ]);

        $ticket = DrawTicket::create([
            'ticket_number' => $request->ticket_number,
            'is_winner' => $request->is_winner ?? false,
        ]);

        return response()->json($ticket, 201);
    }

    // Show a single ticket
    public function show($id)
    {
        $ticket = DrawTicket::findOrFail($id);
        return response()->json($ticket);
    }

    // Update a ticket
    public function update(Request $request, $id)
    {
        $ticket = DrawTicket::findOrFail($id);

        $request->validate([
            'ticket_number' => 'string|unique:draw_tickets,ticket_number,' . $ticket->id,
            'is_winner' => 'boolean',
        ]);

        $ticket->update($request->only(['ticket_number', 'is_winner']));

        return response()->json($ticket);
    }

    // Delete a ticket
    public function destroy($id)
    {
        $ticket = DrawTicket::findOrFail($id);
        $ticket->delete();

        return response()->json(['message' => 'Ticket deleted successfully']);
    }

    // Bulk import from CSV
    public function importCsv(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
        ]);

        $file = $request->file('file');
        $path = $file->getRealPath();
        $data = array_map('str_getcsv', file($path));

        if (empty($data)) {
            return response()->json(['message' => 'CSV is empty'], 400);
        }

        // Assuming first row is header
        $header = array_map('strtolower', $data[0]);
        $rows = array_slice($data, 1);

        $imported = [];
        $errors = [];

        foreach ($rows as $index => $row) {
            $rowData = array_combine($header, $row);

            $validator = Validator::make($rowData, [
                'ticket_number' => 'required|string|unique:draw_tickets,ticket_number',
                'is_winner' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                $errors[$index + 2] = $validator->errors()->all(); // +2 for CSV line number
                continue;
            }

            $imported[] = DrawTicket::create([
                'ticket_number' => $rowData['ticket_number'],
                'is_winner' => $rowData['is_winner'] ?? false,
            ]);
        }

        return response()->json([
            'imported_count' => count($imported),
            'errors' => $errors,
        ]);
    }
}
