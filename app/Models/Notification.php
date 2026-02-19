<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', 'type', 'title', 'message',
        'payload', 'status', 'attempts', 'last_error', 'processed_at'
    ];

    protected $casts = [
        'payload' => 'array',
        'processed_at' => 'datetime'
    ];
    
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
