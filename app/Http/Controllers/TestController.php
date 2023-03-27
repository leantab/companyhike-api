<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Leantab\Sherpa\Facades\Sherpa;
use Leantab\Sherpa\Models\Game;
use Route;

class TestController extends Controller
{

    public function index($current = null)
    {

        $routes = [
            'user' => [
                'method' => 'GET',
                'url' => '/api/user'
            ],
            'notifications' => [
                'method' => 'GET',
                'url' => '/api/notifications'
            ],
            'update_profile' => [
                'method' => 'POST',
                'url' => '/api/user/update_profile',
                'params' => [
                    'name' => 'textarea',
                    'lastname' => 'textarea',
                    'email' => 'textarea'
                ]
            ],
            'match-schema' => [
                'method' => 'GET',
                'url' => '/api/match/schema',
            ],
            'create-match' => [
                'method' => 'POST',
                'url' => '/api/match/create',
                'params' => [
                    'community_id' => 'integer',
                    'version' => 'textarea',
                    'data' => 'textarea'
                ]
            ],
            'match-add-company' => [
                'method' => 'POST',
                'url' => '/api/match/add_company',
                'params' => [
                    'match_id' => 'integer',
                    'company_name' => 'textarea',
                    'avatar' => 'integer'
                ]
            ],
            'match-list' => [
                'method' => 'GET',
                'url' => '/api/match/list/{community_id}',
                'params' => [
                    'community_id' => 'integer'
                ]
            ],
            'match-preview' => [
                'method' => 'GET',
                'url' => '/api/match/preview/{match_id}',
                'params' => [
                    'match_id' => 'integer'
                ]
            ],
            'match-info' => [
                'method' => 'GET',
                'url' => '/api/match/info/{match_id}',
                'params' => [
                    'match_id' => 'integer'
                ]
            ],

            'community' => [
                'method' => 'GET',
                'url' => '/api/community/{community_id}',
                'params' => [
                    'community_id' => 'integer'
                ]
            ],
            'communityMembers' => [
                'method' => 'GET',
                'url' => '/api/community/members/{community_id}',
                'params' => [
                    'community_id' => 'integer'
                ]
            ],
            'community_invite' => [
                'method' => 'POST',
                'url' => '/api/community_invite/{community_id}',
                'params' => [
                    'community_id' => 'integer',
                    'name' => 'textarea',
                    'lastname' => 'textarea',
                    'email' => 'textarea'
                ]
            ],
            'accept-community-invitation' => [
                'method' => 'GET',
                'url' => '/api/accept_invitation/{invitation_id}',
                'params' => [
                    'invitation_id' => 'textarea'
                ]
            ],
            'match_invite' => [
                'method' => 'POST',
                'url' => '/api/match_invite',
                'params' => [
                    'match_id' => 'integer',
                    'community_id' => 'integer',
                    'user_id' => 'integer',
                    'name' => 'textarea',
                    'lastname' => 'textarea',
                    'email' => 'textarea'
                ]
            ],
            'accept-match-invitation' => [
                'method' => 'GET',
                'url' => '/api/accept_match_invitation/{invitation_id}',
                'params' => [
                    'invitation_id' => 'textarea'
                ]
            ],
        ];

        if ($current) {
            $current_api = $routes[$current];
        } else {
            $current_api = false;
        }

        return view('apitest.api-functions', [
            'routes' => $routes,
            'current_api' => $current_api,
            'current' => $current,
        ]);
    }

    public function createTestGameScenario($version)
    {
        $game = Sherpa::createTestGameScenario($version);
        return view('gameTest');
    }

    public function getCeoVariables($game_id, $user_id)
    {
        $vars = Sherpa::getCeoVariables($game_id, $user_id);
        dd($vars);
    }
}
