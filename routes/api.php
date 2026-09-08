<?php

use App\Http\Controllers\Api\DepartemenController;
use App\Http\Controllers\Api\KaryawanController;
use App\Http\Controllers\Api\KategoriPengeluaranController;
use App\Http\Controllers\Api\PengeluaranPerjalananController;
use App\Http\Controllers\Api\ProyekController;
use App\Http\Controllers\Api\TujuanController;
use Illuminate\Support\Facades\Route;

// Health check endpoint
Route::get('/test', function () {
    return response()->json([
        'success' => true,
        'message' => 'Astronova API Ready',
        'data' => null,
    ]);
});

// Master Data & Transaksi Ringan (Backend Developer 1)
Route::apiResource('departemen', DepartemenController::class);
Route::apiResource('karyawan', KaryawanController::class);
Route::apiResource('proyek', ProyekController::class);
Route::apiResource('tujuan', TujuanController::class);
Route::apiResource('kategori-pengeluaran', KategoriPengeluaranController::class);
Route::apiResource('pengeluaran-perjalanan', PengeluaranPerjalananController::class);