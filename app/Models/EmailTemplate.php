<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $table = 'ft_email_templates';
    
    protected $primaryKey = 'email_id';
    
    public $timestamps = false;
    
    protected $fillable = [
        'form_id',
        'email_template_name',
        'email_status',
        'view_mapping_type',
        'view_mapping_view_id',
        'limit_email_content_to_fields_in_view',
        'email_trigger_set',
        'subject',
        'email_from',
        'email_from_account_id',
        'email_from_form_email_id',
        'custom_from_name',
        'custom_from_email',
        'email_reply_to',
        'email_reply_to_account_id',
        'email_reply_to_form_email_id',
        'custom_reply_to_name',
        'custom_reply_to_email',
        'html_template',
        'text_template',
    ];

    protected $casts = [
        'email_status' => 'string',
        'view_mapping_type' => 'string',
        'email_trigger_set' => 'string',
        'email_from' => 'string',
        'email_reply_to' => 'string',
    ];
}
