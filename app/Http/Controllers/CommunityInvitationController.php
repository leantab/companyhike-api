<?php

namespace App\Http\Controllers;

use App\Mail\CommunityInvitation as MailCommunityInvitation;
use App\Mail\InvitationEmail;
use Illuminate\Http\Request;
use App\Models\CommunityInvitation;
use App\Models\Community;
use App\Models\User;
use App\Notifications\CommunityInvitation as NotificationsCommunityInvitation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CommunityInvitationController extends Controller
{

    /**
     * @OA\Post(
     *   path="/api/community_invite/{community}",
     *   @OA\Parameter(
     *      in="path",
     *      name="community",
     *      description="ID de Comunidad"
     *   ),
     *   tags={"Community Invitation"},
     *   security={ {"bearer": {}} },
     *   @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="multipart/form-data",
     *          @OA\Schema(
     *              @OA\Property(property="email", type="string", example="admin@companyhike.com"),
     *              @OA\Property(property="name", type="string", example="companyhike"),
     *              @OA\Property(property="lastname", type="string", example="companyhike")
     *          )
     *      )
     *   ),
     *   @OA\Response(response=200, description="JSON true"),
     *   @OA\Response(response=403, description="JWT inválido"),
     * )
     */
    public function invite(Request $request, Community $community)
    {
        //$this->authorize('access_segment', $community);

        $valid = $request->validate([
            'name' => 'required|max:255',
            'lastname' => 'required|max:255',
            'email' => 'required|email|max:255',
        ]);

        #Check invitaciones previas. Si ya tiene una aceptada, no deja enviar otra. Si tiene pendientes, se borran
        $prev_inv = CommunityInvitation::where([['community_id', $community->id], ['email', $request->email]])->get();
        foreach ($prev_inv as $inv) {
            if ($inv->accepted == 1) {
                return response()->json(['status' => false, 'message' => 'invitación ya enviada']);
            } else {
                if ($inv->accepted == null) {
                    $inv->delete();
                }
            }
        }

        $inv = CommunityInvitation::create([
            'community_id' => $community->id,
            'name' => $request->name,
            'lastname' => $request->lastname,
            'email' => $request->email,
        ]);

        $user = User::where('email', $request->email)->first();
        if ($user != null) {
            # NOTIFICACION!!!!!
            $url = route('accept_invitation', [encrypt($inv->id)]);
            $user->notify((new NotificationsCommunityInvitation($inv, $url))->delay([
                'database' => now(),
                'mail' => now()->addMinutes(10),
            ]));
        } else {
            # ENVIAR EMAIL
            // $url = route('community_registration', [encrypt($community->id), encrypt($inv->id)]);
            $url = 'http://localhost:3000/register?community_id='.encrypt($community->id).'&invitation_id='.encrypt($inv->id);
            Mail::to($request->email)->send(new MailCommunityInvitation($community, $url));
        }

        return response()->json(true);
    }

    /**
     * @OA\Get(
     *   path="/api/accept_invitation/{invitation_id}",
     *   @OA\Parameter(
     *      in="path",
     *      name="invitation_id",
     *      description="ID de invitación a la Comunidad ENCRIPTADO"
     *   ),
     *   tags={"Community Invitation"},
     *   security={ {"bearer": {}} },
     *   @OA\Response(response=200, description="JSON true"),
     *   @OA\Response(response=403, description="JWT inválido"),
     * )
     */
    public function acceptInvitation($invitation_id)
    {
        $inv = CommunityInvitation::find(decrypt($invitation_id));

        $com = Community::find($inv->community_id);
        $user = User::find(auth()->id());

        if ($inv->accepted != true) {

            $user->acceptCommunityInvitation($inv->id);

            try {
                $noti = $user->notifications()->where('data->invitation_id', $inv->id)->first();
                $noti->markAsRead();
            } catch (\Exception $e) {
                Log::error('[Error Buscando la notificacion] ' . $e->getMessage());
            }
        } else {
            abort(400);
        }

        return response()->json(true);
    }
}
