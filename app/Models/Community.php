<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Community extends Model
{
    use HasFactory;
    protected $guarded = [];


    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot([
            'user_id', 'community_id', 'role_id', 'created_at', 'updated_at'
        ]);
    }

    public function games()
    {
        return $this->hasMany(Game::class, 'segment_id');
    }

    public function admins()
    {
        return $this->belongsToMany(User::class)->withPivot([
            'user_id', 'community_id', 'role_id', 'created_at', 'updated_at'
        ])->wherePivot('role_id', 1);
    }

    public function licenses()
    {
        return $this->hasMany(License::class);
    }

    public function activeLicense()
    {
        return $this->hasOne(License::class)->where('is_active', true);
    }

    public function hasActiveLicenseAttribute(): bool
    {
        return (bool) $this->activeLicense()->exists();
    }
}
