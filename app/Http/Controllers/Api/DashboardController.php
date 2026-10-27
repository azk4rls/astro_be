<?php

namespace App\Http\Controllers\Api;

use App\Enums\StatusPengajuanEnum;
use App\Enums\StatusPenggantianEnum;
use App\Models\Booking;
use App\Models\Departemen;
use App\Models\Karyawan;
use App\Models\KategoriPengeluaran;
use App\Models\LogAudit;
use App\Models\PengajuanPenggantian;
use App\Models\PengajuanPerjalananTugas;
use App\Models\PengeluaranPerjalanan;
use App\Models\Proyek;
use App\Models\Settlement;
use App\Models\TravelPolicy;
use App\Models\Tujuan;
use App\Models\UangMuka;
use App\Models\Vendor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends BaseApiController
{
    /**
     * Mengambil statistik komprehensif dashboard secara dinamis dari database.
     * Tidak ada angka palsu, dummy, atau mock data.
     */
    public function stats(Request $request): JsonResponse
    {
        // 1. Agregasi Pengajuan Perjalanan
        $pengajuanQuery = PengajuanPerjalananTugas::query();
        if ($request->filled('tahun')) {
            $pengajuanQuery->whereYear('tanggal_mulai', $request->integer('tahun'));
        }

        $totalPerjalanan = (clone $pengajuanQuery)->count();
        $draftPerjalanan = (clone $pengajuanQuery)->where('status', StatusPengajuanEnum::DRAFT->value)->count();
        $menungguPerjalanan = (clone $pengajuanQuery)->where('status', StatusPengajuanEnum::MENUNGGU_PERSETUJUAN->value)->count();
        $disetujuiPerjalanan = (clone $pengajuanQuery)->where('status', StatusPengajuanEnum::DISETUJUI->value)->count();
        $ditolakPerjalanan = (clone $pengajuanQuery)->where('status', StatusPengajuanEnum::DITOLAK->value)->count();
        $totalEstimasiBiaya = (float) ((clone $pengajuanQuery)->sum('estimasi_biaya') ?? 0);
        $totalUangMukaDiajukan = (float) ((clone $pengajuanQuery)->sum('uang_muka') ?? 0);

        // 2. Agregasi Pengajuan Penggantian (Reimbursement)
        $reimbursementQuery = PengajuanPenggantian::query();
        if ($request->filled('tahun')) {
            $reimbursementQuery->whereYear('tanggal_pengajuan', $request->integer('tahun'));
        }

        $totalReimbursement = (clone $reimbursementQuery)->count();
        $draftReimbursement = (clone $reimbursementQuery)->where('status', StatusPenggantianEnum::DRAFT->value)->count();
        $menungguReimbursement = (clone $reimbursementQuery)->where('status', StatusPenggantianEnum::MENUNGGU_PERSETUJUAN->value)->count();
        $disetujuiReimbursement = (clone $reimbursementQuery)->where('status', StatusPenggantianEnum::DISETUJUI->value)->count();
        $ditolakReimbursement = (clone $reimbursementQuery)->where('status', StatusPenggantianEnum::DITOLAK->value)->count();
        $totalKlaimReimbursement = (float) ((clone $reimbursementQuery)->sum('total_pengeluaran') ?? 0);
        $totalKurangBayar = (float) ((clone $reimbursementQuery)->where('selisih', '>', 0)->sum('selisih') ?? 0);
        $totalLebihBayar = (float) ((clone $reimbursementQuery)->where('selisih', '<', 0)->sum('selisih') ?? 0);
        $totalNihil = (clone $reimbursementQuery)->where('selisih', 0)->count();

        // 3. Agregasi Pengeluaran Riil
        $pengeluaranQuery = PengeluaranPerjalanan::query();
        if ($request->filled('tahun')) {
            $pengeluaranQuery->whereYear('tanggal_pengeluaran', $request->integer('tahun'));
        }
        $totalPengeluaranRiil = (float) ((clone $pengeluaranQuery)->sum('jumlah') ?? 0);
        $totalTransaksiPengeluaran = (clone $pengeluaranQuery)->count();
        $pengeluaranSudahDiganti = (clone $pengeluaranQuery)->where('sudah_diganti', true)->count();
        $pengeluaranBelumDiganti = (clone $pengeluaranQuery)->where('sudah_diganti', false)->count();

        // 4. Operasional Pendukung (Uang Muka, Booking, Settlement)
        $uangMukaDicairkan = (float) (UangMuka::where('status', 'dicairkan')->sum('disetujui') ?? 0);
        $totalBooking = Booking::count();
        $bookingConfirmed = Booking::where('status', 'confirmed')->count();
        $bookingPending = Booking::where('status', 'pending')->count();
        $totalSettlement = Settlement::count();
        $settlementSelesai = Settlement::where('status', 'selesai')->count();

        // 5. Master Data Counts
        $masterCounts = [
            'total_karyawan' => Karyawan::count(),
            'total_departemen' => Departemen::count(),
            'total_proyek' => Proyek::count(),
            'total_tujuan' => Tujuan::count(),
            'total_vendor' => Vendor::count(),
            'total_travel_policy' => TravelPolicy::count(),
        ];

        // 6. Pengeluaran per Kategori
        $kategoriBreakdown = KategoriPengeluaran::withCount('pengeluaranPerjalanan')
            ->withSum('pengeluaranPerjalanan', 'jumlah')
            ->get()
            ->map(fn ($k) => [
                'id_kategori' => $k->id_kategori,
                'nama_kategori' => $k->nama_kategori,
                'total_transaksi' => $k->pengeluaran_perjalanan_count,
                'total_biaya' => (float) ($k->pengeluaran_perjalanan_sum_jumlah ?? 0),
            ]);

        // 7. Aktivitas Audit Log Terbaru (5 data terakhir)
        $aktivitasTerbaru = LogAudit::with('user')
            ->orderByDesc('id_log')
            ->limit(5)
            ->get()
            ->map(fn ($log) => [
                'id_log' => $log->id_log,
                'user' => $log->user?->name ?? 'Sistem',
                'action' => $log->action,
                'model_type' => $log->model_type,
                'model_id' => $log->model_id,
                'waktu' => $log->created_at?->toIso8601String(),
            ]);

        return $this->sendSuccess('Statistik dashboard berhasil diambil.', [
            'perjalanan' => [
                'total' => $totalPerjalanan,
                'draft' => $draftPerjalanan,
                'menunggu_persetujuan' => $menungguPerjalanan,
                'disetujui' => $disetujuiPerjalanan,
                'ditolak' => $ditolakPerjalanan,
                'total_estimasi_biaya' => $totalEstimasiBiaya,
                'total_uang_muka_diajukan' => $totalUangMukaDiajukan,
            ],
            'reimbursement' => [
                'total' => $totalReimbursement,
                'draft' => $draftReimbursement,
                'menunggu_persetujuan' => $menungguReimbursement,
                'disetujui' => $disetujuiReimbursement,
                'ditolak' => $ditolakReimbursement,
                'total_klaim' => $totalKlaimReimbursement,
                'total_kurang_bayar' => $totalKurangBayar,
                'total_lebih_bayar' => $totalLebihBayar,
                'total_nihil' => $totalNihil,
            ],
            'pengeluaran' => [
                'total_biaya_aktual' => $totalPengeluaranRiil,
                'total_transaksi' => $totalTransaksiPengeluaran,
                'sudah_diganti' => $pengeluaranSudahDiganti,
                'belum_diganti' => $pengeluaranBelumDiganti,
            ],
            'operasional' => [
                'uang_muka_dicairkan' => $uangMukaDicairkan,
                'total_booking' => $totalBooking,
                'booking_confirmed' => $bookingConfirmed,
                'booking_pending' => $bookingPending,
                'total_settlement' => $totalSettlement,
                'settlement_selesai' => $settlementSelesai,
            ],
            'master' => $masterCounts,
            'kategori_breakdown' => $kategoriBreakdown,
            'aktivitas_terbaru' => $aktivitasTerbaru,
        ]);
    }

    /**
     * Ringkasan cepat untuk widget kartu KPI di dashboard.
     */
    public function ringkasan(): JsonResponse
    {
        $totalPerjalanan = PengajuanPerjalananTugas::count();
        $persetujuanTertunda = PengajuanPerjalananTugas::where('status', StatusPengajuanEnum::MENUNGGU_PERSETUJUAN->value)->count()
            + PengajuanPenggantian::where('status', StatusPenggantianEnum::MENUNGGU_PERSETUJUAN->value)->count();
        $totalPengeluaranRiil = (float) (PengeluaranPerjalanan::sum('jumlah') ?? 0);
        $totalReimbursementDisetujui = (float) (PengajuanPenggantian::where('status', StatusPenggantianEnum::DISETUJUI->value)->sum('total_pengeluaran') ?? 0);

        return $this->sendSuccess('Ringkasan KPI berhasil diambil.', [
            'total_perjalanan' => $totalPerjalanan,
            'persetujuan_tertunda' => $persetujuanTertunda,
            'total_pengeluaran_riil' => $totalPengeluaranRiil,
            'total_reimbursement_disetujui' => $totalReimbursementDisetujui,
        ]);
    }
}
