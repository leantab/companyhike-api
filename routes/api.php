<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\CommunityInvitationController;
use App\Http\Controllers\GameInvitationController;
use App\Http\Controllers\MatchInvitationController;
use App\Http\Controllers\GamesController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group([
    'middleware' => ['api'],
    'prefix' => 'auth'
], function ($router) {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class, 'me']);
});


Route::get('/swagger', function () {
    require(base_path("/vendor/autoload.php"));
    $openapi = \OpenApi\Generator::scan([base_path('app/Http/Controllers')]);
    return $openapi->toJSON();
});

Route::get('/game/create_test', [GamesController::class, 'createTestGameScenario']);

Route::group([
    'middleware' => ['auth:api'],
], function () {
    Route::get('/user', [UserController::class, 'get_user']);
    Route::get('/notifications', [UserController::class, 'getNotifications']);
    Route::post('/user/update_profile', [UserController::class, 'updateProfile']);
    Route::get('/user/get_user_data', [UserController::class, 'getUserData']);
    Route::post('/user/update_user_data', [UserController::class, 'setUserData']);

    Route::get('/community/{id}', [CommunityController::class, 'getCommunity']);
    Route::get('/community/members/{id}', [CommunityController::class, 'communityMemebers']);

    Route::post('/community_invite/{community}', [CommunityInvitationController::class, 'invite']);
    Route::get('/accept_invitation/{invitation_id}', [CommunityInvitationController::class, 'acceptInvitation'])->name('accept_invitation');

    Route::post('/match_invite', [GameInvitationController::class, 'invite']);
    Route::get('/accept_match_invitation/{invitation_id}', [GameInvitationController::class, 'acceptInvitation'])->name('accept_match_invitation');
    Route::get('/match_invitations/{match_id}', [GameInvitationController::class, 'getInvitations']);

    Route::post('/game_invite', [GameInvitationController::class, 'invite']);
    Route::get('/accept_game_invitation/{invitation_id}', [GameInvitationController::class, 'acceptInvitation'])->name('accept_game_invitation');
    Route::get('/game_invitations/{game_id}', [GameInvitationController::class, 'getInvitations']);
    Route::post('/delete_invitation', [GameInvitationController::class, 'deleteInvitation']);

    Route::get('/match/schema',  [GamesController::class, 'game_schema']);
    Route::get('/match/list/{community_id}', [GamesController::class, 'list']);
    Route::get('/match/preview/{game_id}',  [GamesController::class, 'game_preview']);
    Route::get('/match/info/{game_id}', [GamesController::class, 'info']);
    Route::post('/match/create', [GamesController::class, 'create']);
    Route::post('/match/add_company', [GamesController::class, 'addCeo']);
    Route::get('/match/get_goverment_parameters/{game_id}/{game_step}', [GamesController::class, 'getGovermentParameters']);
    Route::post('/match/set_goverment_parameters', [GamesController::class, 'setGovermentParameters']);
    Route::get('/match/get_ceo_parameters/{game_id}/{game_step}', [GamesController::class, 'getCeoParameters']);
    Route::post('/match/set_ceo_parameters', [GamesController::class, 'setCeoParameters']);
    Route::get('/match/get_game_ranking/{game_id}/{game_step}', [GamesController::class, 'getGameRanking']);
    Route::post('/match/delete_game', [GamesController::class, 'deleteGame']);
    Route::post('/match/delete_ceo', [GamesController::class, 'deleteCeo']);
    Route::post('/match/process_game', [GamesController::class, 'processGame']);
    Route::post('/match/reprocess_game', [GamesController::class, 'reprocessGame']);
    Route::post('/match/force_process_game', [GamesController::class, 'forceProcessGame']);

    Route::get('/game/schema',  [GamesController::class, 'game_schema']);
    Route::get('/game/list/{community_id}', [GamesController::class, 'list']);
    Route::get('/game/preview/{game_id}',  [GamesController::class, 'game_preview']);
    Route::get('/game/info/{game_id}', [GamesController::class, 'info']);
    Route::post('/game/create', [GamesController::class, 'create']);
    Route::post('/game/add_company', [GamesController::class, 'addCeo']);
    Route::get('/game/get_goverment_parameters/{game_id}/{game_step}', [GamesController::class, 'getGovermentParameters']);
    Route::post('/game/set_goverment_parameters', [GamesController::class, 'setGovermentParameters']);
    Route::get('/game/get_ceo_parameters/{game_id}/{game_step}', [GamesController::class, 'getCeoParameters']);
    Route::post('/game/set_ceo_parameters', [GamesController::class, 'setCeoParameters']);
    Route::get('/game/get_game_ranking/{game_id}/{game_step}', [GamesController::class, 'getGameRanking']);
    Route::post('/game/delete_game', [GamesController::class, 'deleteGame']);
    Route::post('/game/delete_ceo', [GamesController::class, 'deleteCeo']);
    Route::post('/game/process_game', [GamesController::class, 'processGame']);
    Route::post('/game/reprocess_game', [GamesController::class, 'reprocessGame']);
    Route::post('/game/force_process_game', [GamesController::class, 'forceProcessGame']);


});

Route::get('/image/{type}/{id}', [ImageController::class, 'get']);
Route::post('/image/store', [ImageController::class, 'store']);
