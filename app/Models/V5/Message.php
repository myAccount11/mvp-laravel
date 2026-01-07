<?php

namespace App\Models\V5;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $table = 'messages';

    const CREATED_AT = 'sent';
    const UPDATED_AT = null;

    protected $fillable = [
        'type_id',
        'html',
        'user_id',
        'to_id',
        'restriction',
        'notification_time',
        'email',
        'subject',
    ];

    protected $casts = [
        'type_id' => 'integer',
        'user_id' => 'integer',
        'to_id' => 'integer',
        'restriction' => 'integer',
        'notification_time' => 'datetime',
    ];

    public function writer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function attachments()
    {
        return $this->hasMany(MessageAttachment::class, 'message_id');
    }
}

