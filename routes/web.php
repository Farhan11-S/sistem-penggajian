<?php

use App\Http\Controllers\PDFController;
use Illuminate\Support\Facades\Route;

// Route::get('pdf', function () {
//     return 'Export PDF';
// })->name('pdf');
Route::get('view-rincian-gaji/{id}', [PDFController::class, 'rincianGaji'])->name('rincian-gaji');
// Route::get('view-laporan-lembur', [PDFController::class, 'laporanLembur'])->name('laporan-lembur');