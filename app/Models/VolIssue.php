<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VolIssue extends Model
{
    protected $table = 'vol_issue';

    public $timestamps = false;
    
    protected $fillable = [
        'vol',
        'issues',
        'Month',
        'Year',
        'issues_type',
        'deleted',
    ];
}
