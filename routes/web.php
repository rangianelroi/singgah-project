<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DisposalReportController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/admin/disposal-reports/{record}/download', [DisposalReportController::class, 'download'])
    ->middleware('auth') // Pastikan hanya user yang login bisa akses
    ->name('disposal.report.download');
