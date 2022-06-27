<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserHandicapRequest extends Model
{
    use HasFactory;
    protected $table = 'user_handicap_requests';
    protected $fillable = [
        'user_id',
        'status'
    ];
    public function user() {
        return $this->belongsTo(User::class);
    }
}
