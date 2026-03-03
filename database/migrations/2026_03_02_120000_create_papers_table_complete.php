<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('papers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Paper Information
            $table->string('title');
            
            // Corresponding Author Details
            $table->string('corresponding_author_name');
            $table->string('corresponding_author_email');
            $table->string('contact_no')->nullable();
            $table->string('affiliation')->nullable();
            
            // Position/Post
            $table->string('position');
            
            // Country
            $table->string('country_name')->nullable();
            
            // Paper File
            $table->string('file_path');
            
            // Optional fields
            $table->text('description')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('papers');
    }
};
