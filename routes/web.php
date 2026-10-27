<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'success' => true,
        'service' => 'Astronova Business Travel & Reimbursement API',
        'version' => '1.0.0',
        'status' => 'operational',
        'endpoints' => [
            'login' => url('/api/login'),
            'dashboard_stats' => url('/api/dashboard/stats'),
            'dashboard_ringkasan' => url('/api/dashboard/ringkasan'),
            'laporan_pengeluaran' => url('/api/laporan/pengeluaran'),
            'laporan_perjalanan' => url('/api/laporan/perjalanan'),
            'laporan_reimbursement' => url('/api/laporan/reimbursement'),
            'pengajuan_perjalanan' => url('/api/pengajuan-perjalanan'),
            'pengeluaran' => url('/api/pengeluaran-perjalanan'),
            'penggantian' => url('/api/pengajuan-penggantian'),
            'master_data' => [
                'departemen' => url('/api/departemen'),
                'karyawan' => url('/api/karyawan'),
                'proyek' => url('/api/proyek'),
                'tujuan' => url('/api/tujuan'),
                'kategori_pengeluaran' => url('/api/kategori-pengeluaran'),
                'kebijakan' => url('/api/travel-policy'),
                'vendors' => url('/api/vendor'),
            ],
        ],
    ]);
});
