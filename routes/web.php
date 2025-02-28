<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportExportController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/reports/export/{id}', [ReportExportController::class, 'exportPdf'])->name('reports.exportPdf');
