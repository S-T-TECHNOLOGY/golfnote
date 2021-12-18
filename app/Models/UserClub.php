<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserClub extends Model
{
    use HasFactory;
    protected $table = 'user_clubs';
    protected $fillable = [
        'user_id',
        'name',
        'introduction',
        'images',
        'kakaotalk_link'
    ];

    protected function user()
    {
        return $this->belongsTo(User::class);
    }
}
