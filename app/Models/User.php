<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class User extends Authenticatable implements HasMedia
{
    use HasApiTokens, HasFactory, Notifiable, InteractsWithMedia;

    protected $fillable = [
        'name',
        'middle_name',
        'last_name',
        'email',
        'phone',
        'permission',
        'role',
        'profile_description',
        'firebase_token',
        'views',
        'is_subscribed',
        'is_seamstress',
        'is_customer',
        'email_code',
        'reset_password_code',
        'password',
        'apple_id',
        'apple_expires_date',
        'apple_transaction_id',
        'apple_is_subscribe',
        'google_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_subscribed' => 'boolean',
        'is_seamstress' => 'boolean',
        'is_customer' => 'boolean',
        'email_verified_at' => 'datetime',
        'apple_expires_date' => 'datetime',
        'reset_password_code' => 'hashed',
        'password' => 'hashed',
    ];

    protected $with = ['reviews'];

    public function getRatingAttribute()
    {
        $reviews = $this->reviews;

        if ($reviews->isEmpty()) {
            return null;
        }

        return round($reviews->avg('rating'), 1);
    }

    public function incrementViews()
    {
        $this->update([
            'views' => $this->views + 1,
        ]);

        return true;
    }

    public function resetViews()
    {
        $this->update([
            'views' => 0,
        ]);

        return true;
    }

    public function userOrders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function seamstressOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'seamstress_id');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activities::class);
    }

    public function sizes(): BelongsToMany
    {
        return $this->belongsToMany(Size::class)->withPivot('value');
    }

    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class);
    }

    public function notepads(): HasMany
    {
        return $this->hasMany(Notepad::class);
    }

    public function permissions(): HasMany
    {
        return $this->hasMany(Permission::class);
    }

    public function accessibleItems()
    {
        return $this->permissions()->with('permissible');
    }

    public function notepadPermissions()
    {
        return $this->permissions()->where('permissible_type', Notepad::class);
    }

    public function reviews(): HasManyThrough
    {
        return $this->hasManyThrough(
            Review::class,
            Order::class,
            'user_id',
            'order_id',
            'id',
            'id'
        );
    }

}
