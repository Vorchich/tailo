<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class Task extends Model
//  implements Sortable
{
    use
    // SortableTrait,
    HasFactory;

    protected $fillable = [
        'order_id',
        'title',
        'description',
        'status',
        'order_column',
        'position',
    ];

    public $sortable = [
        'order_column_name' => 'order_column',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
