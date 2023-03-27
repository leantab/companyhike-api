<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatchInvitation extends Model
{
    use HasFactory;

    // protected $table = 'game_invitations';
    protected $guarded = [];

    public function match()
    {
        return $this->belongsTo(Game::class);
    }

    public function community()
    {
        return $this->belongsTo(Community::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
