<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Text extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'text',
    ];

    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function permissions(): MorphMany
    {
        return $this->morphMany(Permission::class, 'permissible');
    }
}
