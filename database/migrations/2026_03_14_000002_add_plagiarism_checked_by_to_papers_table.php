<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('papers', function (Blueprint $table) {
            $table->unsignedBigInteger('plagiarism_checked_by')->nullable()->after('plagiarism_percentage');
        });
    }

    public function down(): void
    {
        Schema::table('papers', function (Blueprint $table) {
            $table->dropColumn('plagiarism_checked_by');
        });
    }
};
