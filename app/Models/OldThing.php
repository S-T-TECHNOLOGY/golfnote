<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OldThing extends Model
{
    use HasFactory;
    protected $table = 'old_things';
    protected $fillable = [
        'user_id',
        'name',
        'quantity',
        'quantity_remain',
        'image',
        'description',
        'price',
        'sale_off'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
