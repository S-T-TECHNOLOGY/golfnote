<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailOtp extends Model
{
    use HasFactory;
    protected $table = 'mail_otps';
    protected $fillable = [
        'user_id',
        'code',
        'type'
    ];

}
