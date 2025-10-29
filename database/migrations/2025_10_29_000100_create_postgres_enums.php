<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("DO $$ BEGIN
            IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'role_type') THEN
                CREATE TYPE role_type AS ENUM ('admin','petugas');
            END IF;
        END $$;");

        DB::statement("DO $$ BEGIN
            IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'status_type') THEN
                CREATE TYPE status_type AS ENUM ('menunggu','dipanggil','selesai');
            END IF;
        END $$;");
    }

    public function down(): void
    {
        DB::statement("DO $$ BEGIN
            IF EXISTS (SELECT 1 FROM pg_type WHERE typname = 'status_type') THEN
                DROP TYPE status_type;
            END IF;
        END $$;");

        DB::statement("DO $$ BEGIN
            IF EXISTS (SELECT 1 FROM pg_type WHERE typname = 'role_type') THEN
                DROP TYPE role_type;
            END IF;
        END $$;");
    }
};
