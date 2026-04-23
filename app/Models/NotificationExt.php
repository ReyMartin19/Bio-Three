<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationExt extends Model
{
    protected $table = 'notification_exts';

    protected $primaryKey = 'notification_id';

    protected $fillable = [
        'userid',
        'category',
        'message',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function userinfo(): BelongsTo
    {
        return $this->belongsTo(Userinfo::class, 'userid', 'Userid');
    }
}
