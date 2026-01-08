<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;

class TruncateController extends Controller
{
    //
    public function truncateEmployees()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        Ticket::truncate(); 
        Employee::truncate();    

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        return response()->json(['message' => 'Employees table truncated successfully.']);
    }

    public function truncateDrawTickets()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
          DB::statement('TRUNCATE TABLE cache');
          DB::statement('TRUNCATE TABLE cache_locks');
          DB::statement('TRUNCATE TABLE categories');
          DB::statement('TRUNCATE TABLE draw_tickets');
          DB::statement('TRUNCATE TABLE draw_winners');
          DB::statement('TRUNCATE TABLE draws');
          DB::statement('TRUNCATE TABLE failed_jobs');
          DB::statement('TRUNCATE TABLE job_batches');
          DB::statement('TRUNCATE TABLE jobs');
          DB::statement('TRUNCATE TABLE migrations');
          DB::statement('TRUNCATE TABLE password_reset_tokens');
          DB::statement('TRUNCATE TABLE prizes');
          DB::statement('TRUNCATE TABLE sessions');
          DB::statement('TRUNCATE TABLE users');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');

        return response()->json(['message' => 'Draw Tickets table truncated successfully.']);
    }
}