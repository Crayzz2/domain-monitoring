<?php

use Illuminate\Support\Facades\Route;

//Route::get('/', function () {
//    return view('welcome');
//});

Route::get('/print', [\App\Http\Controllers\PrintSummaryController::class, 'print'])->name('relatório.pdf');
