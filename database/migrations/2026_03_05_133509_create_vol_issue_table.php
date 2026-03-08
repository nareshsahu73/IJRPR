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
        Schema::create('vol_issue', function (Blueprint $table) {

            $table->increments('id'); // AUTO_INCREMENT + PRIMARY KEY

            $table->integer('vol')->nullable();
            $table->integer('issues')->nullable();

            $table->string('Month',30)->nullable();
            $table->string('Year',10)->nullable();

            $table->string('issues_type',30)->nullable();

            $table->tinyInteger('deleted')->default(0);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vol_issue');
    }
};
