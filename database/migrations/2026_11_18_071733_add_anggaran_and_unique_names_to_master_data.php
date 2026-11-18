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
        if (! Schema::hasColumn('proyek', 'anggaran')) {
            Schema::table('proyek', function (Blueprint $table): void {
                $table->decimal('anggaran', 15, 2)->nullable();
            });
        }

        $uniqueNames = [
            'klien' => ['nama_klien', 'klien_nama_klien_unique'],
            'jenis_pekerjaan' => ['nama_pekerjaan', 'jenis_pekerjaan_nama_pekerjaan_unique'],
        ];

        foreach ($uniqueNames as $tableName => [$columnName, $indexName]) {
            if (Schema::hasIndex($tableName, $indexName)) {
                continue;
            }

            $hasDuplicates = DB::table($tableName)
                ->select($columnName)
                ->groupBy($columnName)
                ->havingRaw('COUNT(*) > 1')
                ->exists();

            if ($hasDuplicates) {
                throw new RuntimeException("Cannot add a unique index to {$tableName}.{$columnName} while duplicate values exist.");
            }

            Schema::table($tableName, function (Blueprint $table) use ($columnName, $indexName): void {
                $table->unique($columnName, $indexName);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ([
            ['klien', 'klien_nama_klien_unique'],
            ['jenis_pekerjaan', 'jenis_pekerjaan_nama_pekerjaan_unique'],
        ] as [$tableName, $indexName]) {
            if (Schema::hasIndex($tableName, $indexName)) {
                Schema::table($tableName, function (Blueprint $table) use ($indexName): void {
                    $table->dropUnique($indexName);
                });
            }
        }

        // Keep anggaran if it now contains user-entered project budgets.
    }
};
