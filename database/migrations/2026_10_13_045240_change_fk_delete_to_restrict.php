<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->replaceDeleteRule('karyawan', 'restrict');
        $this->replaceDeleteRule('pengeluaran_perjalanan', 'restrict');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->replaceDeleteRule('karyawan', 'cascade');
        $this->replaceDeleteRule('pengeluaran_perjalanan', 'cascade');
    }

    /**
     * Replace the ON DELETE rule of the parent foreign key.
     *
     * SQLite cannot drop or re-add foreign keys on an existing table,
     * so the table is rebuilt (temp table, copy data, drop, rename).
     * Other engines use a plain dropForeign + re-add.
     */
    private function replaceDeleteRule(string $table, string $action): void
    {
        if (DB::getDriverName() === 'sqlite') {
            $this->rebuildSqliteTable($table, $action);

            return;
        }

        $parent = $table === 'karyawan' ? 'departemen' : 'kategori_pengeluaran';
        $column = $table === 'karyawan' ? 'id_departemen' : 'id_kategori';
        $parentColumn = $table === 'karyawan' ? 'id_departemen' : 'id_kategori';

        Schema::table($table, function (Blueprint $table) use ($column, $parent, $parentColumn, $action) {
            $table->dropForeign([$column]);

            $foreign = $table->foreign($column)->references($parentColumn)->on($parent);

            if ($action === 'restrict') {
                $foreign->restrictOnDelete();
            } else {
                $foreign->cascadeOnDelete();
            }
        });
    }

    /**
     * Rebuild a SQLite table with the same schema but a new ON DELETE rule.
     */
    private function rebuildSqliteTable(string $table, string $action): void
    {
        $temp = $table.'_new';

        if ($table === 'karyawan') {
            Schema::create($temp, function (Blueprint $table) use ($action) {
                $table->bigIncrements('id_karyawan');
                $table->string('nik', 50)->unique();
                $table->string('nama_lengkap', 150);
                $table->string('email', 150)->unique();
                $table->string('telepon', 30)->nullable();

                $foreign = $table->foreignId('id_departemen')->constrained('departemen', 'id_departemen');

                if ($action === 'restrict') {
                    $foreign->restrictOnDelete();
                } else {
                    $foreign->cascadeOnDelete();
                }

                $table->string('peran', 100);
                $table->timestamps();
            });
        } else {
            Schema::create($temp, function (Blueprint $table) use ($action) {
                $table->bigIncrements('id_pengeluaran');
                $table->unsignedBigInteger('id_pengajuan')->nullable()->index();

                $foreign = $table->foreignId('id_kategori')->constrained('kategori_pengeluaran', 'id_kategori');

                if ($action === 'restrict') {
                    $foreign->restrictOnDelete();
                } else {
                    $foreign->cascadeOnDelete();
                }

                $table->date('tanggal_pengeluaran');
                $table->text('deskripsi')->nullable();
                $table->decimal('jumlah', 15, 2);
                $table->string('metode_pembayaran', 50);
                $table->boolean('sudah_diganti')->default(false);
                $table->timestamps();
            });
        }

        $this->finishSqliteRebuild($table, $temp);
    }

    /**
     * Copy data from the old table to the rebuilt one and swap the names.
     */
    private function finishSqliteRebuild(string $table, string $temp): void
    {
        $columns = Schema::getColumnListing($table);

        DB::table($temp)->insertUsing($columns, DB::table($table)->select($columns));

        Schema::drop($table);
        Schema::rename($temp, $table);
    }
};
