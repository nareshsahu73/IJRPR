<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Only set if current max id is less than 200000
        $maxId = DB::table('papers')->max('id') ?? 0;
        if ($maxId < 200000) {
            DB::statement('ALTER TABLE papers AUTO_INCREMENT = 200000');
        }
    }

    public function down(): void
    {
        // irreversible
    }
};
