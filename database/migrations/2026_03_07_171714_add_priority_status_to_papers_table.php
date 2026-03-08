<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('papers', function (Blueprint $table) {
            $table->enum('priority_status', [
                'High priority UG',
                'Medium Priority PG',
                'Medium Priority Academic',
                'Low Priority Abroad'
            ])->default('Medium Priority PG')->after('Keywords');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('papers', function (Blueprint $table) {
            $table->dropColumn('priority_status');
        });
    }
};
