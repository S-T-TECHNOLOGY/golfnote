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
        'total_course',
        'total_partner',
        'high_score',
        'total_score',
        'total_hio',
        'total_fail',
        'total_punish',
        'visited_score',
        'handicap_score',
    ];
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
