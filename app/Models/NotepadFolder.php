<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class NotepadFolder extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable = [
        'name',
        'text',
    ];

    protected $with = ['media', 'texts'];

    public function notepad(): BelongsTo
    {
        return $this->belongsTo(Notepad::class);
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
