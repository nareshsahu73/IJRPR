<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Paper extends Model
{
    use HasFactory;

    protected $fillable = [
        'Title',
        'Author',
        'Abstract',
        'Volume',
        'Issue',
        'PageFrom',
        'PageTo',
        'file_name',
        'DOI',
        'publication_date',
        'Reference',
        'Keywords',
        'author_name',
        'more_data',
        'cer_author_name',
        'cer_date',
        'cer_status',
        'certificate_only',
        'created_by',
        'deleted',
        'contact_no',
        'affiliation',
        'position',
        'paper_status',
        'final_manuscript',
        'copy_right_received',
        'filled_copy_right',
        'status_of_payment',
        'invoice_no',
        'certificate_link',
        'formatted_doc',
        'plagiarism_report',
        'plagiarism_percentage',
        'email_template_id',
    ];
    
    protected $appends = ['vol_issue_id'];

    public $timestamps = false;
    
    public function getVolIssueIdAttribute()
    {
        if ($this->Volume && $this->Issue) {
            $volIssue = \App\Models\VolIssue::where('vol', $this->Volume)
                ->where('issues', $this->Issue)
                ->first();
            return $volIssue ? $volIssue->id : null;
        }
        return null;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
