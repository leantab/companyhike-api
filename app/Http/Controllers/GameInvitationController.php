<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Community;
use App\Models\Game;
use App\Models\GameInvitation;
use App\Models\User;
use App\Notifications\MatchInvitationNotification;
use App\Mail\MatchInvitationMail;
use Illuminate\Support\Facades\Log;
// use CompanyHike\Sherpa\Sherpa;
use Illuminate\Support\Facades\Mail;

class GameInvitationController extends Controller
{
    /**
     * @OA\Post(
     *   path="/api/game_invite",
     *   tags={"Game Invitation"},
     *   security={ {"bearer": {}} },
     *   @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="multipart/form-data",
     *          @OA\Schema(
     *              @OA\Property(property="match_id", type="integer", example="1"),
     *              @OA\Property(property="community_id", type="integer", example="1"),
     *              @OA\Property(property="user_id", type="integer", example="1"),
     *              @OA\Property(property="email", type="string", example="admin@companyhike.com"),
     *              @OA\Property(property="name", type="string", example="companyhike"),
     *              @OA\Property(property="lastname", type="string", example="companyhike"),
     *          )
     *      )
     *   ),
     *   @OA\Response(response=200, description="JSON true"),
     *   @OA\Response(response=403, description="JWT inválido"),
     * )
     */
    public function invite(Request $request)
    {
        $request->validate([
            'match_id' => 'required',
            'community_id' => 'required',
            'user_id' => 'nullable',
            'name' => 'required_without:user_id',
            'lastname' => 'required_without:user_id',
            'email' => 'required_without:user_id',
        ]);

        $admin = User::find(auth()->id());
        $game = Game::find($request->match_id);

        /*if (!$admin->isGameAdmin($game)) {
            return response()->json(['status' => false, 'message' => 'El usuario no administra esta partida'], 403);
        }*/

        $com = Community::find($request->community_id);

        if ($request->has('user_id') && $request->user_id != '') {
            $user = User::find($request->user_id);
        }elseif ($request->has('email') && $request->email != '') {
            $user = User::where('email', $request->email)->first();
        }else{
            $user = null;
        }

        if ($user == null) {
            # Invitacion usuario nuevo
            $prev_inv = GameInvitation::where([['game_id', $game->id], ['email', $request->email]])->get();
            foreach ($prev_inv as $inv) {
                if ($inv->accepted == 1) {
                    return response()->json(['status' => false, 'message' => 'invitación ya aceptada']);
                } else {
                    if ($inv->accepted == null) {
                        $inv->delete();
                    }
                }
            }

            $inv = GameInvitation::create([
                'game_id' => $game->id,
                'community_id' => $com->id,
                'name' => $request->name,
                'lastname' => $request->lastname,
                'email' => $request->email,
            ]);

            # ENVIAR EMAIL
            // $url = route('match_registration', [encrypt($community->id), encrypt($inv->id)]);
            $url = 'loclahost:3000/register/' . encrypt($com->id) .'/'. encrypt($inv->id);
            Mail::to($request->email)->send(new MatchInvitationMail($com, $url));

        }else{

            $prev_inv = GameInvitation::where([ ['user_id', $user->id], ['game_id', $game->id] ])->get();
            foreach ($prev_inv as $inv) {
                if ($inv->accepted == 1) {
                    return response()->json(['status' => false, 'message' => 'invitación ya enviada'], 403);
                } else {
                    if ($inv->accepted == null) {
                        $inv->delete();
                    }
                }
            }

            $inv = GameInvitation::create([
                'game_id' => $game->id,
                'community_id' => $com->id,
                'user_id' => $user->id,
                'email' => $user->email,
            ]);

            $url = route('accept_match_invitation', [encrypt($inv->id)]);
            $user->notify((new MatchInvitationNotification($inv, $url))->delay([
                'database' => now(),
                'mail' => now()->addMinutes(10),
            ]));
        }


        return response()->json(['status' => true, 'mesage' => 'Invitación enviada']);

    }

    /**
     * /**
     * @OA\Get(
     *   path="/api/accept_game_invitation/{invitation_id}",
     *   @OA\Parameter(
     *      in="path",
     *      name="invitation_id",
     *      description="ID de invitación a la partida ENCRIPTADO"
     *   ),
     *   tags={"Game Invitation"},
     *   security={ {"bearer": {}} },
     *   @OA\Response(response=200, description="JSON true"),
     *   @OA\Response(response=403, description="JWT inválido"),
     * )
     */
    public function acceptInvitation($invitation_id)
    {
        $inv = GameInvitation::findOrFail(decrypt($invitation_id));

        /** @var User $user */
        $user = User::find(auth()->id());

        if ($inv->accepted == true) {
            abort(403);
        }

        $user->acceptGameInvitation($inv);

        try {
            $noti = $user->notifications()->where('data->match_invitation_id', $inv->id)->first();
            if ($noti) {
                $noti->markAsRead();
            }
        } catch (\Exception $e) {
            Log::error('[Error Buscando la notificacion] ' . $e->getMessage());
        }

        return response()->json('true');
    }

    /**
     * /**
     * @OA\Get(
     *   path="/api/game_invitations/{game_id}",
     *   @OA\Parameter(
     *      in="path",
     *      name="game_id",
     *      description="ID de la partida"
     *   ),
     *   tags={"Game Invitation"},
     *   security={ {"bearer": {}} },
     *   @OA\Response(response=200, description="JSON con las invitaciones a la partida"),
     *   @OA\Response(response=403, description="JWT inválido"),
     * )
     */
    public function getInvitations($game_id)
    {
        $game = Game::findOrFail($game_id);

        $invitations = GameInvitation::where('game_id', $game->id)->get();

        $invitations = $invitations->map(function ($inv) {
            if ($inv->user_id != null) {
                $inv->user = User::find($inv->user_id);
            }
            return $inv;
        });

        return response()->json($invitations);
    }

    /**
     * @OA\Post(
     *   path="/api/delete_invitation",
     *   tags={"Game Invitation"},
     *   security={ {"bearer": {}} },
     *   @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="multipart/form-data",
     *          @OA\Schema(
      *              @OA\Property(property="invitation_id", type="integer", example="1"),
     *          )
     *      )
     *   ),
     *   @OA\Response(response=200, description="JSON true"),
     *   @OA\Response(response=403, description="JWT inválido"),
     * )
     */
    public function deleteInvitation(Request $request)
    {
        $request->validate([
            'invitation_id' => 'required|integer',
        ]);

        $invitation = GameInvitation::findOrFail($request->invitation_id);
        $invitation->delete();

        return response()->json('true');
    }
}
