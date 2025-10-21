<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Actions\PublicNotificationAction;
class TopicNotification extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'body',
    ];


    protected static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            $action = new PublicNotificationAction();
            $action->handle($model->title,$model->body);
        });
    }
}
