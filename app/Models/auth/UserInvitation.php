<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use App\Models\Auth\Role;

class UserInvitation extends Model
{
    use HasFactory;

    protected $table = 'invitations';
    protected $fillable = ['email', 'role_id', 'token', 'expires_at', 'accepted', 'invited_by'];

    protected $casts = [
        'expires_at' => 'datetime',
        'accepted' => 'boolean',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function inviter()
    {
        return $this->belongsTo(User::class, 'invited_by');
    }
}