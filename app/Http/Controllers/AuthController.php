<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;


// use App\Actions\Fortify\CreateNewUser;

class AuthController extends Controller
{
    /**
     * -
     *
     * @return void
     *
     * @OA\Info(title="CompanyHike API", version="1.0")
     * @OA\SecurityScheme(
     *      securityScheme="bearer",
     *      in="header",
     *      name="Authorization",
     *      type="http",
     *      scheme="bearer",
     *      bearerFormat="JWT",
     * ),
     *
     *
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *   path="/api/auth/login",
     *     tags={"Auth"},
     *   @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="multipart/form-data",
     *          @OA\Schema(
     *              @OA\Property(property="email", type="string", example="admin@companyhike.com"),
     *              @OA\Property(property="password", type="string", example="companyhike"),
     *          )
     *      )
     *   ),
     *   @OA\Response(response=200, description="JSON con el JWT Token"),
     *   @OA\Response(response=403, description="Errores de autenticación")
     * )
     *
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     *  @OA\Post(
     *   path="/api/auth/me",
     *   tags={"Auth"},
     *   security={ {"bearer": {}} },
     *   @OA\Response(response=200, description="Datos del usuario"),
     *   @OA\Response(response=403, description="Errores de autenticación")
     * )
     */
    public function me()
    {
        return response()->json(auth('api')->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth('api')->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }

    /**
     * Create a new user and return a token
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     *
     *
     * @OA\Post(
     *   path="/api/auth/register",
     *     tags={"Auth"},
     *   @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="multipart/form-data",
     *          @OA\Schema(
     *              @OA\Property(property="name", type="string"),
     *              @OA\Property(property="lastname", type="string"),
     *              @OA\Property(property="email", type="string"),
     *              @OA\Property(property="password", type="string"),
     *              @OA\Property(property="password_confirmation", type="string"),
     *              @OA\Property(property="language", type="string", description="[en / es / pt]"),
     *              @OA\Property(property="community_invitation", type="string", description="ID de invitación a comunidad ENCRIPTADO"),
     *              @OA\Property(property="match_invitation", type="string", description="ID de invitación a partida ENCRIPTADO")
     *          )
     *      )
     *   ),
     *   @OA\Response(response=200, description="Cliente creado. Json con datos del cliente"),
     *   @OA\Response(response=400, description="Errores de validación")
     * )
     */
    public function register(Request $request)
    {
        Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ])->validate();

        $user = User::create([
            'name' => $request->input('name'),
            'lastname' => $request->input('lastname'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);

        if ($request->has('language')) {
            $user->language = $request->input('language');
            $user->save();
        }

        if ($request->has('community_invitation') && $request->input('community_invitation') != '') {
            $user->acceptCommunityInvitation(decrypt($request->input('community_invitation')));
        }

        if ($request->has('match_invitation') && $request->input('match_invitation') != '') {
            $user->acceptMatchInvitation(decrypt($request->input('match_invitation')));
        }

        return $this->respondWithToken(auth('api')->attempt(['email' => $request->input('email'), 'password' => $request->input('password')]));
        // return response()->json($user);
    }
}
