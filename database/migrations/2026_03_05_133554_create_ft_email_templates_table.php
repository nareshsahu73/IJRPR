<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
     public function up(): void
    {
        Schema::create('ft_email_templates', function (Blueprint $table) {

            $table->mediumIncrements('email_id');

            $table->unsignedMediumInteger('form_id');

            $table->string('email_template_name',100)->nullable();

            $table->enum('email_status',['enabled','disabled'])->default('enabled');

            $table->enum('view_mapping_type',['all','specific'])->default('all');

            $table->mediumInteger('view_mapping_view_id')->nullable();

            $table->mediumInteger('limit_email_content_to_fields_in_view')->nullable();

            $table->set('email_event_trigger',['on_submission','on_edit','on_delete'])->nullable();

            $table->enum('include_on_edit_submission_page',['no','all_views','specific_views'])->default('no');

            $table->string('subject',255)->nullable();

            $table->enum('email_from',['admin','client','form_email_field','custom','none'])->nullable();

            $table->unsignedMediumInteger('email_from_account_id')->nullable();

            $table->unsignedMediumInteger('email_from_form_email_id')->nullable();

            $table->string('custom_from_name',100)->nullable();

            $table->string('custom_from_email',100)->nullable();

            $table->enum('email_reply_to',['admin','client','form_email_field','custom','none'])->nullable();

            $table->unsignedMediumInteger('email_reply_to_account_id')->nullable();

            $table->unsignedMediumInteger('email_reply_to_form_email_id')->nullable();

            $table->string('custom_reply_to_name',100)->nullable();

            $table->string('custom_reply_to_email',100)->nullable();

            $table->mediumText('html_template')->nullable();

            $table->mediumText('text_template')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ft_email_templates');
    }
};
