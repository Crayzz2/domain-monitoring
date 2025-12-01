<?php

use Illuminate\Support\Facades\Route;

//Route::get('/', function () {
//    return view('welcome');
//});

Route::get('/print', [\App\Http\Controllers\PrintSummaryController::class, 'print'])->name('print');
Route::get('/print-expired/{slug?}', [\App\Http\Controllers\PrintSummaryController::class, 'printExpired'])->name('print-expired');
