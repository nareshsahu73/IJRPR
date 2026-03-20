<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE papers 
            MODIFY priority_status ENUM(
                'High priority UG',
                'Medium Priority PG',
                'Medium Priority Academic',
                'Low Priority Abroad'
            ) NULL
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE papers 
            MODIFY priority_status ENUM(
                'High priority UG',
                'Medium Priority PG',
                'Medium Priority Academic',
                'Low Priority Abroad'
            ) DEFAULT 'Medium Priority PG'
        ");
    }
};