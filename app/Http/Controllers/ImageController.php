<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    /**
     * @OA\Get(
     *   path="/api/image/{type}/{id}",
     *   @OA\Parameter(in="path", name="type", description="avatar/community/match/user", required=true, @OA\Schema(type="string")),
     *   @OA\Parameter(in="path", name="id",description="ID del usuario o comunidad", required=true),
     *   tags={"Images"},
     *   security={ {"bearer": {}} },
     *   @OA\Response(response=200, description="imagen"),
     * )
     */
    public function get($type, $id)
    {
        if (Storage::exists('public/' . $type . '/' . $id . '.jpg')) {
            $file = Storage::get('public/' . $type . '/' . $id . '.jpg');
        }elseif (Storage::exists('public/' . $type . '/' . $id . '.png')) {
            $file = Storage::get('public/' . $type . '/' . $id . '.png');
        }elseif (Storage::exists('public/' . $type . '/' . $id . '.jpeg')) {
            $file = Storage::get('public/' . $type . '/' . $id . '.jpeg');
        }else{
            return response()->json(['error' => 'File not found'], 404);
        }

        return response($file)->header('Content-Type', 'image/jpeg');
    }

    /**
     * @OA\Post(
     *   path="/api/image/store",
     *   tags={"Images"},
     *   security={ {"bearer": {}} },
     *   @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="multipart/form-data",
     *          @OA\Schema(
     *              @OA\Property(property="file", type="file"),
     *              @OA\Property(property="type", type="string", example="avatar/community/match/user"),
     *              @OA\Property(property="id", type="string", example="1")
     *          )
     *      )
     *   ),
     *   @OA\Response(response=200, description="josn: {status: true}"),
     *   @OA\Response(response=403, description="Errores de autenticación"),
     *   @OA\Response(response=422, description="Errores de validación")
     * )
     */
    public function store(Request $request)
    {
        $this->validate(
            $request,
            [
                'file' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                'type' => 'required',
                'id' => 'required',
            ]
        );

        $file = $request->file('file');
        $ext = $file->clientExtension();
        $path = $file->storeAs('public/' . $request->type, $request->id . '.' . $ext);
        return response()->json(['path' => $path]);
    }
}
