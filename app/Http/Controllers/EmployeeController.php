<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    // 🔹 GET: All Employees
    public function index()
    {
        $data = DB::select("
        SELECT 
            e.*,
            GROUP_CONCAT(t.ticket_no ORDER BY t.ticket_no SEPARATOR ', ') AS tickets
        FROM employees e
        INNER JOIN tickets t 
            ON e.id = t.employee_id
        WHERE t.status = 'active'
        GROUP BY e.id
    ");

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }


    // 🔹 POST: Store Employee
    public function store(Request $request)
    {
        $validated = $request->validate([
            'reg_code'     => 'required|string|unique:employees,reg_code',
            'name'         => 'required|string',
            'company'      => 'required|string',
            'branch'       => 'required|string',
            'division'     => 'required|string',
            'department'   => 'required|string',
            'designation'  => 'required|string',
            'gender'       => 'required|string',

            'tickets'      => 'required|array|min:1',
            'tickets.*'    => 'required|string|distinct|unique:tickets,ticket_no',
        ]);

        DB::beginTransaction();

        try {
            // 1️⃣ Create Employee
            $employee = Employee::create([
                'branch'      => $validated['branch'],
                'division'    => $validated['division'],
                'reg_code'    => $validated['reg_code'],
                'name'        => $validated['name'],
                'department'  => $validated['department'],
                'designation' => $validated['designation'],
                'company'     => $validated['company'],
                'gender'      => $validated['gender'],
            ]);

            // 2️⃣ Prepare tickets data
            $tickets = collect($validated['tickets'])->map(function ($ticketNo) {
                return [
                    'ticket_no'  => $ticketNo,
                    'status'     => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })->toArray();

            // 3️⃣ Save tickets using relationship
            $employee->tickets()->createMany($tickets);

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Employee & tickets created successfully',
                'data'    => $employee->load('tickets'),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }


    // 🔹 GET: Single Employee
    public function show($id)
    {
        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json([
                'status' => false,
                'message' => 'Employee not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $employee
        ]);
    }

    // 🔹 PUT: Update Employee
    public function update(Request $request, $id)
    {
        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json([
                'status' => false,
                'message' => 'Employee not found'
            ], 404);
        }

        $validated = $request->validate([
            'reg_code' => 'required|unique:employees,reg_code,' . $employee->id,
            'name' => 'required|string',
            'branch' => 'nullable|string',
            'division' => 'nullable|string',
            'department' => 'nullable|string',
            'designation' => 'nullable|string',
            'company' => 'nullable|string',
            'gender' => 'nullable|in:male,female,other',
        ]);

        $employee->update($validated);

        return response()->json([
            'status' => true,
            'message' => 'Employee updated successfully',
            'data' => $employee
        ]);
    }

    // 🔹 DELETE: Remove Employee
    public function destroy($id)
    {
        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json([
                'status' => false,
                'message' => 'Employee not found'
            ], 404);
        }

        $employee->delete();

        return response()->json([
            'status' => true,
            'message' => 'Employee deleted successfully'
        ]);
    }

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

                if (empty($data['reg_code']) || empty($data['ticket_no'])) {
                    continue;
                }

                // 1️⃣ Employee create/get
                $employee = Employee::firstOrCreate(
                    ['reg_code' => $data['reg_code']],
                    [
                        'branch' => $data['branch'] ?? null,
                        'division' => $data['division'] ?? null,
                        'name' => $data['name'] ?? '',
                        'department' => $data['department'] ?? null,
                        'designation' => $data['designation'] ?? null,
                        'company' => $data['company'] ?? null,
                        'gender' => $data['gender'] ?? null,
                    ]
                );

                // 2️⃣ Ticket create (only ticket_no)
                Ticket::firstOrCreate(
                    ['ticket_no' => $data['ticket_no']],
                    [
                        'employee_id' => $employee->id,
                        'status' => 'active'
                    ]
                );
            }
        });

        return response()->json([
            'message' => 'CSV imported successfully'
        ]);
    }

    public function searchByTicket($ticket_no)
    {
        if (empty($ticket_no)) {
            return response()->json([
                'status' => false,
                'message' => 'Ticket number is required'
            ], 422);
        }
        $ticketNo = trim($ticket_no);

        $employee = DB::table('tickets as t')
            ->join('employees as e', 'e.id', '=', 't.employee_id')
            ->where('t.ticket_no', $ticketNo)
            ->where('t.status', 'active')
            ->select(
                'e.id',
                'e.name',
                'e.branch',
                'e.division',
                'e.department',
                'e.designation',
                'e.reg_code',
                'e.company',
                'e.gender',
                't.ticket_no'
            )
            ->first();

        if (!$employee) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid ticket number'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $employee
        ]);
    }


}
