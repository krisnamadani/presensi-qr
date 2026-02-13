<?php

use Illuminate\Support\Facades\Route;

Route::get('/', [App\Http\Controllers\PresensiController::class, 'index'])->name('presensi.index');
Route::post('/presensi', [App\Http\Controllers\PresensiController::class, 'store'])->name('presensi.store');

Route::get('/autocomplete-peserta', [App\Http\Controllers\PesertaController::class, 'autocomplete'])->name('peserta.autocomplete');
Route::get('/fill-kabupaten', [App\Http\Controllers\PesertaController::class, 'fillkabupaten'])->name('peserta.fillkabupaten');

Route::get('/data-presensi', [App\Http\Controllers\DataPresensiController::class, 'index'])->name('data.presensi.index');