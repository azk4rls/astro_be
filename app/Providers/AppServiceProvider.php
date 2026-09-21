<?php

namespace App\Providers;

use App\Models\BuktiStruk;
use App\Models\DokumenPendukung;
use App\Models\PengajuanPenggantian;
use App\Models\PengajuanPerjalananTugas;
use App\Observers\AuditObserver;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Relation::morphMap([
            'pengajuan_perjalanan' => PengajuanPerjalananTugas::class,
            'pengajuan_penggantian' => PengajuanPenggantian::class,
        ]);

        PengajuanPerjalananTugas::observe(AuditObserver::class);
        PengajuanPenggantian::observe(AuditObserver::class);
        BuktiStruk::observe(AuditObserver::class);
        DokumenPendukung::observe(AuditObserver::class);
    }
}
