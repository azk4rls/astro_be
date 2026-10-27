<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\BuktiStrukController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DelegasiController;
use App\Http\Controllers\Api\DepartemenController;
use App\Http\Controllers\Api\DokumenPendukungController;
use App\Http\Controllers\Api\ItineraryController;
use App\Http\Controllers\Api\KaryawanController;
use App\Http\Controllers\Api\KategoriPengeluaranController;
use App\Http\Controllers\Api\LaporanController;
use App\Http\Controllers\Api\LogAuditController;
use App\Http\Controllers\Api\NotifikasiController;
use App\Http\Controllers\Api\PengajuanPenggantianController;
use App\Http\Controllers\Api\PengajuanPerjalananController;
use App\Http\Controllers\Api\PengeluaranPerjalananController;
use App\Http\Controllers\Api\PenggunaController;
use App\Http\Controllers\Api\PersetujuanController;
use App\Http\Controllers\Api\ProyekController;
use App\Http\Controllers\Api\SettlementController;
use App\Http\Controllers\Api\TravelPolicyController;
use App\Http\Controllers\Api\TujuanController;
use App\Http\Controllers\Api\UangMukaController;
use App\Http\Controllers\Api\VendorController;
use Illuminate\Support\Facades\Route;

// Health check endpoint
Route::get('/test', function () {
    return response()->json([
        'success' => true,
        'message' => 'Astronova API Ready',
        'data' => null,
    ]);
});

// Public routes
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Master Data & Transaksi Ringan (Backend Developer 1)
    Route::apiResource('departemen', DepartemenController::class);
    Route::apiResource('karyawan', KaryawanController::class);
    Route::apiResource('proyek', ProyekController::class);
    Route::apiResource('tujuan', TujuanController::class);
    Route::apiResource('kategori-pengeluaran', KategoriPengeluaranController::class);
    Route::apiResource('pengeluaran-perjalanan', PengeluaranPerjalananController::class);

    // Alur Bisnis Utama Transaksi Perjalanan Dinas (Backend Developer 2)
    // Modul 1: Pengajuan Perjalanan Tugas
    Route::patch('pengajuan-perjalanan/{id}/status', [PengajuanPerjalananController::class, 'updateStatus']);
    Route::apiResource('pengajuan-perjalanan', PengajuanPerjalananController::class);

    // Modul 2: Pengajuan Penggantian (Reimbursement)
    Route::patch('pengajuan-penggantian/{id}/status', [PengajuanPenggantianController::class, 'updateStatus']);
    Route::apiResource('pengajuan-penggantian', PengajuanPenggantianController::class);

    // Modul 3: Riwayat Persetujuan Tugas (Approval)
    Route::post('persetujuan/approve', [PersetujuanController::class, 'approve']);
    Route::post('persetujuan/reject', [PersetujuanController::class, 'reject']);
    Route::get('persetujuan/{targetType}/{targetId}/history', [PersetujuanController::class, 'history']);

    // Modul 4: Bukti Struk Tugas
    Route::post('/bukti-struk', [BuktiStrukController::class, 'store']);
    Route::delete('/bukti-struk/{id}', [BuktiStrukController::class, 'destroy']);
    Route::get('/pengeluaran/{idPengeluaran}/bukti-struk', [BuktiStrukController::class, 'indexByPengeluaran']);

    // Modul 5: Dokumen Pendukung Tugas
    Route::post('/dokumen-pendukung', [DokumenPendukungController::class, 'store']);
    Route::delete('/dokumen-pendukung/{id}', [DokumenPendukungController::class, 'destroy']);
    Route::get('/pengajuan-perjalanan/{idPengajuan}/dokumen', [DokumenPendukungController::class, 'indexByPengajuan']);

    // Modul 6: Log Audit Tugas
    Route::get('/log-audit', [LogAuditController::class, 'index']);
    Route::get('/log-audit/{modelType}/{modelId}', [LogAuditController::class, 'showHistory']);

    // Modul 7: Master & Operasional Tambahan (Vendor, Travel Policy, Pengguna)
    Route::apiResource('vendor', VendorController::class);
    Route::apiResource('travel-policy', TravelPolicyController::class);
    Route::apiResource('pengguna', PenggunaController::class)->parameters(['pengguna' => 'pengguna']);

    // Modul 8: Notifikasi
    Route::patch('notifikasi/read-all', [NotifikasiController::class, 'markAllRead']);
    Route::apiResource('notifikasi', NotifikasiController::class);

    // Modul 9: Delegasi Tugas
    Route::apiResource('delegasi', DelegasiController::class);

    // Modul 10: Itinerary Perjalanan
    Route::apiResource('itinerary', ItineraryController::class);

    // Modul 11: Booking
    Route::patch('booking/{id}/status', [BookingController::class, 'updateStatus']);
    Route::apiResource('booking', BookingController::class);

    // Modul 12: Uang Muka
    Route::post('uang-muka/{id}/cairkan', [UangMukaController::class, 'cairkan']);
    Route::apiResource('uang-muka', UangMukaController::class);

    // Modul 13: Settlement
    Route::patch('settlement/{id}/status', [SettlementController::class, 'updateStatus']);
    Route::apiResource('settlement', SettlementController::class);

    // Modul 14: Dashboard & Statistik Dinamis
    Route::get('dashboard/stats', [DashboardController::class, 'stats']);
    Route::get('dashboard/ringkasan', [DashboardController::class, 'ringkasan']);

    // Modul 15: Laporan Dinamis
    Route::get('laporan/pengeluaran', [LaporanController::class, 'pengeluaran']);
    Route::get('laporan/perjalanan', [LaporanController::class, 'perjalanan']);
    Route::get('laporan/reimbursement', [LaporanController::class, 'reimbursement']);
});
