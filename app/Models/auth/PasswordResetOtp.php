<?php

namespace App\Models\Auth;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordResetOtp extends Model
{
    use HasFactory;

    protected $table = 'password_reset_otps';

    protected $fillable = [
        'user_id',
        'otp_code',
        'channel',
        'expires_at',
        'verified_at',
        'attempts',
        'max_attempts',
        'ip_address',
    ];

    protected $dates = [
        'expires_at',
        'verified_at',
        'created_at',
        'updated_at',
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
