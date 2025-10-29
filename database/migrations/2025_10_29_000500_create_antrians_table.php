<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('antrians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loket_id')->constrained('lokets')->cascadeOnDelete();
            $table->string('nomor_antrian', 10);
            $table->timestamp('waktu_panggil')->nullable();
            $table->timestamps();
            $table->index('loket_id', 'idx_antrian_loket_id');
        });

        DB::statement("ALTER TABLE antrians ADD COLUMN status status_type DEFAULT 'menunggu'");
        DB::statement("CREATE INDEX IF NOT EXISTS idx_antrian_status ON antrians(status)");
    }

    public function down(): void
    {
        DB::statement("DROP INDEX IF EXISTS idx_antrian_status");
        Schema::dropIfExists('antrians');
    }
};
