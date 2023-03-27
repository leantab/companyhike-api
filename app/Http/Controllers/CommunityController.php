<?php

namespace App\Http\Controllers;

use App\Models\Community;
use App\Models\CommunityUser;
use App\Models\User;
use Illuminate\Http\Request;

class CommunityController extends Controller
{
    public function getLogo($id)
    {
        if (file_exists(storage_path('app/logos/' . $id . '.jpg'))) {
            echo header('Content-type: image/jpg');
            echo readfile(storage_path('app/logos/' . $id . '.jpg'));
        } else {
            echo header('Content-type: image/jpg');
            echo readfile(storage_path('app/logos/default.jpg'));
        }
    }

    /**
     * @OA\Get(
     *   path="/api/community/{id}",
     *   @OA\Parameter(
     *      in="path",
     *      name="id",
     *      description="ID de Comunidad"
     *   ),
     *   tags={"Community"},
     *   security={ {"bearer": {}} },
     *   @OA\Response(response=200, description="JSON con datos de la comunidad"),
     * )
     */
    public function getCommunity($id)
    {
        $com = Community::findOrFail($id);
        $user = User::find(auth()->id());

        $arr = [
            'id' => $com->id,
            'name' => $com->name,
            'is_admin' => $user->isCommunityAdmin($com),
        ];

        return response()->json($arr);
    }

    /**
     * @OA\Get(
     *   path="/api/community/members/{id}",
     *   @OA\Parameter(
     *      in="path",
     *      name="id",
     *      description="ID de Comunidad"
     *   ),
     *   tags={"Community"},
     *   security={ {"bearer": {}} },
     *   @OA\Response(response=200, description="JSON con los miembros de la comunidad"),
     *   @OA\Response(response=403, description="JWT inválido"),
     * )
     */
    public function communityMemebers($id)
    {
        $com = Community::findOrFail($id);

        $members = [];
        foreach ($com->users as $user) {
            $members[] = [
                'id' => $user->id,
                'name' => $user->name . ' ' . $user->lastname,
                'email' => $user->email,
                'is_admin' => $user->isCommunityAdmin($com)
            ];
        }

        return response()->json($members);
    }
}
