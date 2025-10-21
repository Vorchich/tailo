<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

class File extends Model
{
    use HasFactory;

    protected $fillable = [
        'fileable_type',
        'fileable_id',
        'path',
        'disk',
        'mime_type',
        'is_optimized',
        'optimized_link',
        'priority',
        'type',
    ];

    public function getPathFileAttribute()
    {
        return Storage::url($this->path);
    }

    protected function src(): Attribute
    {
        return new Attribute(
            get: fn () => $this->path ? Storage::disk($this->disk)->url($this->path) : null,
        );
    }

    public function imgUrl()
    {
        if ($this->path && Storage::disk($this->disk)->url($this->path)) {
            return Storage::disk($this->disk)->url($this->path);
        }
    }

    public function saveImg($photo = null, $path = null)
    {
        if ($photo) {
            if ($this->path) {
                Storage::disk($this->disk)->delete('/' . $this->path);
            }
            if ($this->optimized_link) {
                Storage::disk($this->disk)->delete('/' . $this->optimized_link);
                $this->optimized_link = null;
            }
            $this->is_optimized = false;
            $this->path = $photo->store($path, $this->disk);
            $this->save();
        }
    }

    public function saveFile($file = null, $path = null, $mime_type = null)
    {
        if ($file) {
            if ($this->path) {
                Storage::disk($this->disk)->delete('/' . $this->path);
            }
            $this->mime_type = $mime_type;
            $this->path = $file->store($path, $this->disk);
            $this->save();
        }
    }

    public function deleteFile()
    {
        if ($this->path) {
            Storage::disk($this->disk)->delete('/' . $this->path);
        }
    }

    public function scopeByMimeType($query, $mimeType)
    {
        return $query->where('mime_type', 'LIKE', $mimeType);
    }

    public function scopeImage($query)
    {
        return $query->byMimeType('image/%');
    }

    public function scopeFile($query)
    {
        return $query->where('mime_type', 'NOT LIKE', 'image/%');
    }

    public function scopeGetByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function fileable(): MorphTo
    {
        return $this->morphTo();
    }
}
