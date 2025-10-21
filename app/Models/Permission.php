<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'permissible_id',
        'permissible_type',
        'can_edit',
    ];

    /**
     * Отримання користувача, якому надано доступ.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Отримання пов’язаного елемента (нотатник, папка, файл, текст).
     */
    public function permissible(): MorphTo
    {
        return $this->morphTo();
    }
}
