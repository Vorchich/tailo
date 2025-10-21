<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;


class Category extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable = [
        'name',
        'slug',
    ];

    public function sizes(): BelongsToMany
    {
        return $this->belongsToMany(Size::class);
    }

    public function requiredSizes(): BelongsToMany
    {
        return $this->belongsToMany(Size::class, 'category_size_required');
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
