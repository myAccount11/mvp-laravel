<?php

namespace App\Models\V5;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageRead extends Model
{
    use HasFactory;

    protected $table = 'message_read';

    const CREATED_AT = 'read_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'message_id',
        'user_id',
    ];

    protected $casts = [
        'message_id' => 'integer',
        'user_id' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function message()
    {
        return $this->belongsTo(Message::class, 'message_id');
    }
}

