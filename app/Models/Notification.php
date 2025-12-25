<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\DatabaseNotification as BaseDatabaseNotification;

class Notification extends BaseDatabaseNotification
{
    use HasFactory;

    // DatabaseNotification handles its own fillable attributes, so we don't need to redefine it here.
    // protected $fillable = [
    //     'user_id',
    //     'message',
    //     'link',
    //     'is_read',
    // ];

    // If you need custom relationships or methods, add them here.
    // public function user()
    // {
    //     return $this->belongsTo(User::class);
    // }
}
