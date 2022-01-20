<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSummary extends Model
{
    protected $table = 'score_summaries';
    protected $fillable = [
        'user_id',
        'total_round',
        'avg_score',
        'total_partner',
        'high_score',
        'last_score',
        'total_hio',
        'set_error',
        'punish',
        'visited_score',
        'handicap_score',
    ];
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
