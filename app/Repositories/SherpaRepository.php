<?php

//this repository is used as a simple way to access Leantab\Sherpa\Sherpa::class
namespace App\Repositories;

use \Leantab\Sherpa\Models\Game;
use App\Models\User;
use Leantab\Sherpa\Facades\Sherpa;

class SherpaRepository
{
    public function getSchema()
    {
        return Sherpa::getSchema();
    }

    public function getGames(int $user_id, int $community_id)
    {
        return Sherpa::getGames($user_id, $community_id);
    }

    public function getGame(int $game_id): Game
    {
        return Sherpa::getGame($game_id);
    }

    public function createGame($version, $game_params, $user_id, $community_id)
    {
        return Sherpa::createGame($version, $game_params, $user_id, $community_id);
    }

    public function addGoverment($game_id, $user_id)
    {
        return Sherpa::addGoverment($game_id, $user_id);
    }

    public function addCeo($game_id, $user_id, $company_name, $avatar, $is_funded = false)
    {
        return Sherpa::addCeo($game_id, $user_id, $company_name, $avatar, $is_funded);
    }

    public function addSimpleCeo($game_id, $user_id, $company_name, $avatar)
    {
        return Sherpa::addSimpleCeo($game_id, $user_id, $company_name, $avatar);
    }
}
