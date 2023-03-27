<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameStatus extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'game_statuses';
}
