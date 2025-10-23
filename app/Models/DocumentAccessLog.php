<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentAccessLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'user_id',
        'ip_address',
        'user_agent',
        'action',
        'referrer',
    ];

    /**
     * Get the document that owns the access log
     */
    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    /**
     * Get the user that owns the access log
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}