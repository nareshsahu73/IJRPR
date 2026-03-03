<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Paper extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'corresponding_author_name',
        'corresponding_author_email',
        'contact_no',
        'affiliation',
        'position',
        'country_name',
        'file_path',
        'description',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
