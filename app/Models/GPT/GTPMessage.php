<?php

namespace App\Models\GPT;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
class GTPMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_message',
        'gpt_message',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
