<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // public function up(): void
    // {
    //     Schema::create('papers', function (Blueprint $table) {
    //         $table->id();
    //         $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
    //         // Paper Information
    //         $table->string('title');
            
    //         // Corresponding Author Details
    //         $table->string('corresponding_author_name');
    //         $table->string('corresponding_author_email');
    //         $table->string('contact_no')->nullable();
    //         $table->string('affiliation')->nullable();
            
    //         // Position/Post
    //         $table->string('position');
            
    //         // Country
    //         $table->string('country_name')->nullable();
            
    //         // Paper File
    //         $table->string('file_path');
            
    //         // Optional fields
    //         $table->text('description')->nullable();
            
    //         $table->timestamps();
    //     });
    // }

     public function up(): void
    {
        Schema::create('papers', function (Blueprint $table) {

            $table->increments('id'); // PRIMARY KEY + AUTO_INCREMENT

            $table->string('Title',300)->nullable();
            $table->string('Author',200)->nullable();
            $table->text('Abstract')->nullable();

            $table->integer('Volume')->nullable();
            $table->integer('Issue')->nullable();

            $table->string('PageFrom',255)->nullable();
            $table->string('PageTo',255)->nullable();

            $table->string('file_name',200)->nullable();
            $table->string('DOI',200)->nullable();

            $table->string('publication_date',20)->nullable();

            $table->longText('Reference')->nullable();

            $table->string('Keywords',255)->nullable();

            $table->string('author_name',500)->nullable();

            $table->text('more_data')->nullable();

            $table->text('cer_author_name')->nullable();

            $table->string('cer_date',20)->nullable();

            $table->tinyInteger('cer_status')->default(0);

            $table->tinyInteger('certificate_only')->default(0);

            $table->integer('created_by');

            $table->dateTime('created_at')->useCurrent();
            
            $table->timestamp('updated_at')->nullable();

            $table->tinyInteger('deleted')->default(0);
            
            // Additional fields for form
            $table->string('contact_no', 20)->nullable();
            $table->string('affiliation', 255)->nullable();
            $table->string('position', 100)->nullable();
            
            // Admin fields
            $table->string('paper_status', 50)->nullable();
            $table->string('final_manuscript', 50)->nullable();
            $table->string('copy_right_received', 10)->nullable();
            $table->string('filled_copy_right', 255)->nullable();
            $table->string('status_of_payment', 50)->nullable();
            $table->string('invoice_no', 100)->nullable();
            $table->string('certificate_link', 255)->nullable();
            $table->string('formatted_doc', 255)->nullable();
            $table->string('plagiarism_report', 255)->nullable();
            $table->string('plagiarism_percentage', 255)->nullable();
            $table->integer('email_template_id')->nullable();

        });
    }


    public function down(): void
    {
        Schema::dropIfExists('papers');
    }
};
