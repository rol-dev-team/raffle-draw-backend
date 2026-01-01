<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;

Route::get('/employees', [EmployeeController::class, 'index']);
Route::post('/employees', [EmployeeController::class, 'store']);
Route::get('/employees/{id}', [EmployeeController::class, 'show']);
Route::put('/employees/{id}', [EmployeeController::class, 'update']);
Route::delete('/employees/{id}', [EmployeeController::class, 'destroy']);

Route::post('/employees/import-csv', [EmployeeController::class, 'importCsv']);



use App\Http\Controllers\CategoryController;

//Category Routes
Route::get('/categories', [CategoryController::class, 'index']);
Route::post('/categories', [CategoryController::class, 'store']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);
Route::put('/categories/{id}', [CategoryController::class, 'update']);
Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);

Route::post('/categories/import-csv', [CategoryController::class, 'importCsv']);

use App\Http\Controllers\PrizeController;
//prize

Route::get('/prizes', [PrizeController::class, 'index']);
Route::post('/prizes', [PrizeController::class, 'store']);
Route::get('/prizes/{id}', [PrizeController::class, 'show']);
Route::put('/prizes/{id}', [PrizeController::class, 'update']);
Route::delete('/prizes/{id}', [PrizeController::class, 'destroy']);
Route::post('/prizes/import-csv', [PrizeController::class, 'importCsv']);



use App\Http\Controllers\DrawTicketController;

Route::apiResource('draw-tickets', DrawTicketController::class);
Route::get('/draw-tickets', [DrawTicketController::class, 'index']);
Route::post('/draw-tickets', [DrawTicketController::class, 'store']);
Route::get('/draw-tickets/{id}', [DrawTicketController::class, 'show']);     
Route::put('/draw-tickets/{id}', [DrawTicketController::class, 'update']);   
Route::delete('/draw-tickets/{id}', [DrawTicketController::class, 'destroy']);
// custom CSV import route
Route::post('draw-tickets/import-csv', [DrawTicketController::class, 'importCsv']);



use App\Http\Controllers\TicketController;
// Ticket Routes
Route::get('/tickets', [TicketController::class, 'index']);
Route::post('/tickets', [TicketController::class, 'store']);
Route::get('/tickets/{id}', [TicketController::class, 'show']);     
Route::put('/tickets/{id}', [TicketController::class, 'update']);   
Route::delete('/tickets/{id}', [TicketController::class, 'destroy']);
Route::post('/tickets/import-csv', [TicketController::class, 'importCsv']);


use App\Http\Controllers\ResultHistoryController;

Route::get('/result-histories', [ResultHistoryController::class, 'index']);
Route::post('/result-histories', [ResultHistoryController::class, 'store']);
Route::delete('/result-histories', [ResultHistoryController::class, 'destroyAll']); // optional
