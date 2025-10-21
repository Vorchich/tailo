<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'seamstress_id',
        'category_id',
        'comment',
        'status',
        'seamstress_comfirm',
        'customer_comfirm',
    ];

    public static function getStatuses()
    {
        return ['new' => __('new'), 'in_process' => __('in_process'), 'failed' => __('failed'), 'success' => __('success'), 'pre-order' => __('pre-order')];
    }

    public function notepad(): HasOne
    {
        return $this->hasOne(Notepad::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function seamstress(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function sizes(): BelongsToMany
    {
        return $this->belongsToMany(Size::class)->withPivot('value');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function review(): HasOne
    {
        return $this->HasOne(Review::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

}
