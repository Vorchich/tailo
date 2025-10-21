<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Size extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable = [
        'code',
        'name',
        'link',
        'key',
    ];

    public function getHasVideoAttribute(): bool
    {
        return $this->hasMedia('video');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function requiredCategories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_size_required');
    }

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class)->withPivot('value');
    }

    public function notepads(): BelongsToMany
    {
        return $this->belongsToMany(Notepad::class)->withPivot('value');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot('value');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('preview')
            ->width(700)
            ->height(700);

        $this->addMediaConversion('thumb')
            ->width(300)
            ->height(300);
    }
}
