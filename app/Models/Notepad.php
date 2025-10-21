<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Notepad extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable = [
        'order_id',
        'name',
    ];

    protected $with = ['media', 'texts'];

    public function seamstress(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function notepadFolders(): HasMany
    {
        return $this->hasMany(NotepadFolder::class);
    }

    public function sizes(): BelongsToMany
    {
        return $this->belongsToMany(Size::class)->withPivot('value');
    }

    public function texts(): MorphMany
    {
        return $this->morphMany(Text::class, 'textable');
    }

    public function permissions(): MorphMany
    {
        return $this->morphMany(Permission::class, 'permissible');
    }
}
