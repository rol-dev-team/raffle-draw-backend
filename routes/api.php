<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PrizeController;
use App\Http\Controllers\DrawTicketController;
use App\Http\Controllers\DrawController;
use App\Http\Controllers\TruncateController;

Route::get('/employees', [EmployeeController::class, 'index']);
Route::post('/employees', [EmployeeController::class, 'store']);
Route::get('/employees/{id}', [EmployeeController::class, 'show']);
Route::put('/employees/{id}', [EmployeeController::class, 'update']);
Route::delete('/employees/{id}', [EmployeeController::class, 'destroy']);

Route::post('/employees/import-csv', [EmployeeController::class, 'importCsv']);
Route::get('/employees/search-by-ticket/{ticket_no}', [EmployeeController::class, 'searchByTicket']);




//Category Routes
Route::get('/categories', [CategoryController::class, 'index']);
Route::post('/categories', [CategoryController::class, 'store']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);
Route::put('/categories/{id}', [CategoryController::class, 'update']);
Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);

Route::post('/categories/import-csv', [CategoryController::class, 'importCsv']);


//prize

Route::get('/prizes', [PrizeController::class, 'index']);
Route::post('/prizes', [PrizeController::class, 'store']);
Route::get('/prizes/{id}', [PrizeController::class, 'show']);
Route::put('/prizes/{id}', [PrizeController::class, 'update']);
Route::delete('/prizes/{id}', [PrizeController::class, 'destroy']);
Route::post('/prizes/import-csv', [PrizeController::class, 'importCsv']);




// Draw Ticket Routes
Route::get('/draw-tickets', [DrawTicketController::class, 'index']);
Route::post('/draw-tickets', [DrawTicketController::class, 'store']);
Route::get('/draw-tickets/{id}', [DrawTicketController::class, 'show']);
Route::put('/draw-tickets/{id}', [DrawTicketController::class, 'update'  ]);
Route::delete('/draw-tickets/delete', [DrawTicketController::class, 'destroy']);
Route::post('/draw-tickets/import', [DrawTicketController::class, 'importCsv']);
// Route::apiResource('draw-tickets', DrawTicketController::class);
Route::post('draw-tickets/import-csv', [DrawTicketController::class, 'importCsv']);

Route::post('draw-with-suffle', [DrawController::class, 'drawWithShuffle']);
Route::get('draw-results', [DrawController::class, 'getDrawResults']);



// Ticket Routes
// Route::get('/tickets', [TicketController::class, 'index']);
// Route::post('/tickets', [TicketController::class, 'store']);
// Route::get('/tickets/{id}', [TicketController::class, 'show']);     
// Route::put('/tickets/{id}', [TicketController::class, 'update']);   
// Route::delete('/tickets/{id}', [TicketController::class, 'destroy']);
// Route::post('/tickets/import-csv', [TicketController::class, 'importCsv']);


// table truncate routes for testing purpose
Route::get('/truncate-draw-tickets', [TruncateController::class, 'truncateDrawTickets']);
Route::get('/truncate-employees', [TruncateController::class, 'truncateEmployees']);