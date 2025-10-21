<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SizeUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'size_id',
        'user_id',
        'value',
    ];
}
