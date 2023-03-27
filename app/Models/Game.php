<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class Game extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'games';

    protected $casts = [
        'game_parameters' => 'array',
    ];

    public function community()
    {
        return $this->belongsTo(Community::class, 'segment_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'game_user')->withPivot([
            'company_name',
            'avatar',
            'bankrupt',
            'dismissed',
            'ceo_parameters',
            'results',
            'is_funded',
            'created_at',
            'updated_at',
        ]);
    }

    public function status()
    {
        return $this->belongsTo(GameStatus::class, 'status_id');
    }

    public function getPlayersAttribute()
    {
        return Arr::get($this->game_parameters, 'players', null);
    }

    public function getNameAttribute()
    {
        return Arr::get($this->game_parameters, 'name', null);
    }

    public function getStagesAttribute()
    {
        return Arr::get($this->game_parameters, 'stages', null);
    }

    public function getTypeAttribute()
    {
        return Arr::get($this->game_parameters, 'type', null);
    }

    public function get_user_position(User $user)
    {
        $rel = GameUser::where([['user_id', $user->id], ['game_id', $this->id]])->first();
        if ($rel == null) {
            return false;
        }
        return $rel->current_player_position;
    }

    public function is_admin_player(User $user)
    {
        $rel = GameUser::where([['user_id', $user->id], ['game_id', $this->id]])->first();
        if ($rel == null) {
            return false;
        }
        return $rel->is_match_admin;
    }
}
