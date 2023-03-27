<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UserController extends Controller
{


    /**
     * Retorna información del usuario logueado.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *   path="/api/user",
     *   tags={"User"},
     *   security={ {"bearer": {}} },
     *   @OA\Response(response=200, description="User data"),
     * )
     *
     */
    public function get_user()
    {
        $user = User::find(auth('api')->user()->id);

        $coms = [];
        foreach ($user->communities as $c) {
            $coms[] = [
                'id' => $c->id,
                'name' => $c->name,
                'logo' => '/get_logo/' . $c->id,
                'is_admin' => $user->isCommunityAdmin($c),
            ];
        }
        $arr = [
            'id' => $user->id,
            'name' => $user->name,
            'last_name' => $user->lastname,
            'email' => $user->email,
            'is_ch_admin' => $user->ch_admin ?? false,
            'first_login' => ($user->created_at > Carbon::now()->subHours(2)) ? true : false,
            'pending_match_invitation' => $user->pending_match_invitation,
            'language' => $user->language,
            'user_data' => $user->user_data,
            'communities' => $coms,
            'avatar' => '/images/avatar/' . $user->id,
        ];

        return response()->json($arr);
    }

    /**
     * @OA\Get(
     *   path="/api/notifications",
     *   tags={"User"},
     *   security={ {"bearer": {}} },
     *   @OA\Response(response=200, description="un array con notificaciones"),
     * )
     */
    public function getNotifications()
    {
        $user = User::findOrFail(auth()->id());

        $noti = [];
        foreach ($user->unreadNotifications as $notification) {
            $exp = explode('\\', $notification->type);
            $type = $exp[count($exp) - 1];
            $noti[] = [
                'type' => $type,
                'data' => $notification->data,
            ];
        }

        return response()->json($noti);
    }

    /**
     * @OA\Post(
     *   path="/api/update_profile",
     *   tags={"User"},
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
     *   @OA\Response(response=200, description="josn: {status: true}"),
     *   @OA\Response(response=403, description="Errores de autenticación"),
     *   @OA\Response(response=422, description="Errores de validación")
     * )
     */
    public function updateProfile(Request $req)
    {
        $req->validate([
            'name' => 'required|max:255',
            'lastname' => 'required|max:255',
            'email' => 'required|email',
        ]);

        $user = User::find(auth('api')->id());

        $user->name = $req->name;
        $user->lastname = $req->lastname;

        if ($req->email != $user->email) {
            $usr = User::where([['email', $req->email], ['id', '!=', $user->id]])->get();
            if (count($usr) > 0) {
                return response()->json(['status' => false, 'message' => 'Este email ya se encuentra en uso para otro usuario.']);
            } else {
                $user->email = $req->email;
            }
        }
        $user->save();

        return response()->json(['status' => true]);
    }

    /**
     * @OA\Get(
     *   path="/api/user/get_user_data",
     *   tags={"User"},
     *   security={ {"bearer": {}} },
     *   @OA\Response(response=200, description="JSON con datos del usuario"),
     *   @OA\Response(response=401, description="Error de autenticación"),
     *   @OA\Response(response=403, description="Errores de autenticación"),
     * )
     */
    public function getUserData()
    {
        $data = json_decode(auth('api')->user()->user_data, true);
        return response()->json($data);
    }

    /**
     * @OA\Post(
     *   path="/api/user/update_user_data",
     *   tags={"User"},
     *   security={ {"bearer": {}} },
     *   @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="multipart/form-data",
     *          @OA\Schema(
     *              @OA\Property(property="user_data", type="text", example="{current_community: companyhike}"),
     *          )
     *      )
     *   ),
     *   @OA\Response(response=200, description="josn: {status: true}"),
     *   @OA\Response(response=401, description="Error de autenticación"),
     *   @OA\Response(response=403, description="Errores de autenticación"),
     *   @OA\Response(response=422, description="Errores de validación")
     * )
     */
    public function setUserData(Request $req)
    {
        $user = User::find(auth('api')->id());
        $user->user_data = $req->user_data;
        $user->save();

        return response()->json(['status' => true]);
    }
}
