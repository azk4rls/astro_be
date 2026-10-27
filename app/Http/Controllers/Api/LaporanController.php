<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\PengajuanPenggantianResource;
use App\Http\Resources\PengajuanPerjalananResource;
use App\Http\Resources\PengeluaranPerjalananResource;
use App\Models\PengajuanPenggantian;
use App\Models\PengajuanPerjalananTugas;
use App\Models\PengeluaranPerjalanan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LaporanController extends BaseApiController
{
    /**
     * Laporan dinamik Pengeluaran Perjalanan beserta ringkasan kalkulasi.
     */
    public function pengeluaran(Request $request): JsonResponse
    {
        $query = PengeluaranPerjalanan::with(['kategori', 'buktiStruk']);

        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_pengeluaran', '>=', $request->get('start_date'));
        }

        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_pengeluaran', '<=', $request->get('end_date'));
        }

        if ($request->filled('id_kategori')) {
            $query->where('id_kategori', $request->integer('id_kategori'));
        }

        if ($request->has('sudah_diganti') && $request->get('sudah_diganti') !== null && $request->get('sudah_diganti') !== '') {
            $query->where('sudah_diganti', filter_var($request->get('sudah_diganti'), FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('deskripsi', 'like', "%{$search}%")
                    ->orWhere('metode_pembayaran', 'like', "%{$search}%");
            });
        }

        // Kalkulasi metrik agregat
        $totalBiaya = (float) ((clone $query)->sum('jumlah') ?? 0);
        $totalTransaksi = (clone $query)->count();
        $rataRata = $totalTransaksi > 0 ? round($totalBiaya / $totalTransaksi, 2) : 0.00;

        $perPage = (int) $request->get('per_page', 15);
        $query->orderByDesc('tanggal_pengeluaran');

        if ($request->get('paginate') === 'false') {
            $items = $query->get();
            $data = PengeluaranPerjalananResource::collection($items);
        } else {
            $paginator = $query->paginate($perPage);
            $data = PengeluaranPerjalananResource::collection($paginator)->response()->getData(true);
        }

        return $this->sendSuccess('Laporan pengeluaran berhasil diambil.', [
            'ringkasan' => [
                'total_biaya' => $totalBiaya,
                'total_transaksi' => $totalTransaksi,
                'rata_rata_per_transaksi' => $rataRata,
            ],
            'data' => $data,
        ]);
    }

    /**
     * Laporan dinamik Pengajuan Perjalanan Dinas beserta ringkasan biaya.
     */
    public function perjalanan(Request $request): JsonResponse
    {
        $query = PengajuanPerjalananTugas::with([
            'karyawan.departemen',
            'proyek',
            'tujuan',
            'details.kategori',
        ]);

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->filled('id_karyawan')) {
            $query->where('id_karyawan', $request->integer('id_karyawan'));
        }

        if ($request->filled('id_proyek')) {
            $query->where('id_proyek', $request->integer('id_proyek'));
        }

        if ($request->filled('id_tujuan')) {
            $query->where('id_tujuan', $request->integer('id_tujuan'));
        }

        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_mulai', '>=', $request->get('start_date'));
        }

        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_selesai', '<=', $request->get('end_date'));
        }

        // Kalkulasi metrik agregat
        $totalPerjalanan = (clone $query)->count();
        $totalEstimasiBiaya = (float) ((clone $query)->sum('estimasi_biaya') ?? 0);
        $totalUangMuka = (float) ((clone $query)->sum('uang_muka') ?? 0);
        $rataRataEstimasi = $totalPerjalanan > 0 ? round($totalEstimasiBiaya / $totalPerjalanan, 2) : 0.00;

        $perPage = (int) $request->get('per_page', 15);
        $query->orderByDesc('id_pengajuan');

        if ($request->get('paginate') === 'false') {
            $items = $query->get();
            $data = PengajuanPerjalananResource::collection($items);
        } else {
            $paginator = $query->paginate($perPage);
            $data = PengajuanPerjalananResource::collection($paginator)->response()->getData(true);
        }

        return $this->sendSuccess('Laporan perjalanan berhasil diambil.', [
            'ringkasan' => [
                'total_perjalanan' => $totalPerjalanan,
                'total_estimasi_biaya' => $totalEstimasiBiaya,
                'total_uang_muka' => $totalUangMuka,
                'rata_rata_estimasi' => $rataRataEstimasi,
            ],
            'data' => $data,
        ]);
    }

    /**
     * Laporan dinamik Pengajuan Penggantian (Reimbursement) beserta status klaim.
     */
    public function reimbursement(Request $request): JsonResponse
    {
        $query = PengajuanPenggantian::with([
            'pengajuanPerjalanan.karyawan',
            'details.kategori',
        ]);

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->filled('kondisi_penggantian')) {
            $query->where('kondisi_penggantian', $request->get('kondisi_penggantian'));
        }

        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_pengajuan', '>=', $request->get('start_date'));
        }

        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_pengajuan', '<=', $request->get('end_date'));
        }

        // Kalkulasi metrik agregat
        $totalKlaim = (clone $query)->count();
        $totalPengeluaran = (float) ((clone $query)->sum('total_pengeluaran') ?? 0);
        $totalUangMuka = (float) ((clone $query)->sum('uang_muka') ?? 0);
        $totalKurangBayar = (float) ((clone $query)->where('selisih', '>', 0)->sum('selisih') ?? 0);
        $totalLebihBayar = (float) ((clone $query)->where('selisih', '<', 0)->sum('selisih') ?? 0);

        $perPage = (int) $request->get('per_page', 15);
        $query->orderByDesc('id_penggantian');

        if ($request->get('paginate') === 'false') {
            $items = $query->get();
            $data = PengajuanPenggantianResource::collection($items);
        } else {
            $paginator = $query->paginate($perPage);
            $data = PengajuanPenggantianResource::collection($paginator)->response()->getData(true);
        }

        return $this->sendSuccess('Laporan reimbursement berhasil diambil.', [
            'ringkasan' => [
                'total_klaim' => $totalKlaim,
                'total_pengeluaran' => $totalPengeluaran,
                'total_uang_muka' => $totalUangMuka,
                'total_kurang_bayar' => $totalKurangBayar,
                'total_lebih_bayar' => $totalLebihBayar,
            ],
            'data' => $data,
        ]);
    }
}
