<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    // Get all tickets
    public function index()
    {
        $tickets = Ticket::all();
        return response()->json([
            'status' => true,
            'data' => $tickets
        ]);
    }

    // Get a single ticket by ID
    public function show($id)
    {
        $ticket = Ticket::find($id);

        if (!$ticket) {
            return response()->json([
                'status' => false,
                'message' => 'Ticket not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $ticket
        ]);
    }

    // Create a new ticket
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|integer',
            'ticket_no' => 'required|string|unique:tickets,ticket_no',
            'ticket_type' => 'required|string',
            'issue_date' => 'required|date',
            'expire_date' => 'required|date|after_or_equal:issue_date',
            'price' => 'required|numeric|min:0',
            'status' => 'required|string',
        ]);

        $ticket = Ticket::create($request->only(
            'employee_id',
            'ticket_no',
            'ticket_type',
            'issue_date',
            'expire_date',
            'price',
            'status'
        ));

        return response()->json([
            'status' => true,
            'data' => $ticket
        ], 201);
    }

    // Update an existing ticket
    public function update(Request $request, $id)
    {
        $ticket = Ticket::find($id);

        if (!$ticket) {
            return response()->json([
                'status' => false,
                'message' => 'Ticket not found'
            ], 404);
        }

        $request->validate([
            'employee_id' => 'sometimes|required|integer',
            'ticket_no' => 'sometimes|required|string|unique:tickets,ticket_no,' . $id,
            'ticket_type' => 'sometimes|required|string',
            'issue_date' => 'sometimes|required|date',
            'expire_date' => 'sometimes|required|date|after_or_equal:issue_date',
            'price' => 'sometimes|required|numeric|min:0',
            'status' => 'sometimes|required|string',
        ]);

        $ticket->update($request->only(
            'employee_id',
            'ticket_no',
            'ticket_type',
            'issue_date',
            'expire_date',
            'price',
            'status'
        ));

        return response()->json([
            'status' => true,
            'data' => $ticket
        ]);
    }

    // Delete a ticket
    public function destroy($id)
    {
        $ticket = Ticket::find($id);

        if (!$ticket) {
            return response()->json([
                'status' => false,
                'message' => 'Ticket not found'
            ], 404);
        }

        $ticket->delete();

        return response()->json([
            'status' => true,
            'message' => 'Ticket deleted'
        ]);
    }
}
