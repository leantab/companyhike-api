<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\GameInvitation;
use App\Models\User;
use App\Repositories\SherpaRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Leantab\Sherpa\Facades\Sherpa;

class GamesController extends Controller
{

    private SherpaRepository $sherpaRepository;

    public function __construct(SherpaRepository $sherpaRepository)
    {
        $this->sherpaRepository = $sherpaRepository;
    }

    /**
     * Retorna la definicion del xml para crear una partida
     *
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *   path="/api/game/schema",
     *   tags={"Game (Match2)"},
     *   security={ {"bearer": {}} },
     *   @OA\Response(response=200, description=""),
     * )
     *
     */
    public function game_schema()
    {

        $schema = $this->sherpaRepository->getSchema();
        return response()->json($schema);
    }

    /**
     * Retorna el listado de partidas del usuario logueado.
     *
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *   path="/api/game/list/{community_id}",
     *   tags={"Game (Match2)"},
     *   security={ {"bearer": {}} },
     *      @OA\Parameter(
     *          name="community_id",
     *          description="id de la comunidad",
     *          in="path",
     *          style="form"
     *      ),
     *   @OA\Response(response=200, description=""),
     * )
     *
     */
    public function list($community_id)
    {

        // TODO: SEGURIDAD
        $games = $this->sherpaRepository->getGames(auth()->user()->id, $community_id);
        $inv = GameInvitation::where([['user_id', auth()->user()->id], ['accepted', '!=', 1] ])->get();

        $invitations = [];
        foreach ($inv as $i) {
            $invitations[$i->match_id] = [
                'id' => $i->id,
                'hash' => encrypt($i->id),
                'match_id' => $i->match_id,
                'match_name' => $i->match->name,
                'accepted' => $i->accepted,
                'created_at' => $i->created_at,
                'updated_at' => $i->updated_at,
            ];
        }

        $games = [
            'Match' => $games,
            'MatchInvitations' => $invitations,
        ];

        return response()->json($games);
    }


    /**
     * Retorna información resumida sobre una partida. Para mostrar previo a ingresar al simulador.
     * XML Ejemplo: { "name" : "Test", "players" : 4, "proficiency_rate" : "proficiency_trainee", "industry" : "cars", "type" : "scenario", "scenario" : "argentina_crisis_2001" }
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *   path="/api/game/create",
     *   tags={"Game (Match2)"},
     *   security={ {"bearer": {}} },
     *   @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="multipart/form-data",
     *          @OA\Schema(
     *              @OA\Property(property="community_id", type="string"),
     *              @OA\Property(property="version", type="string"),
     *              @OA\Property(property="data", type="string", example=""),
     *          )
     *      )
     *   ),
     *   @OA\Response(response=200, description=""),
     * )
     *
     */
    public function create(Request $request)
    {
        // TODO: SEGURIDAD & CHECK LISENCIA
        if (is_array($request->input('data'))) {
            $data = json_encode($request->input('data'));
        } else {
            $data = $request->input('data');
        }

        $game = $this->sherpaRepository->createGame(
            $request->version,
            json_decode($data, true),
            auth()->user()->id,
            $request->community_id
        );

        if ($game->status) {
            $this->sherpaRepository->addGoverment($game->id, auth()->user()->id);
        }
        return response()->json($game);
    }

    /**
     * Creates a fake game for testing purposes
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *   path="/api/game/create_test",
     *   tags={"Game (Match2)"},
     *   @OA\Response(response=200, description=""),
     * )
     */
    public function createTestGameScenario(Request $request)
    {
        return response()->json(Sherpa::createTestGameScenario() );
    }


    /**
     * Retorna información resumida sobre una partida. Para mostrar previo a ingresar al simulador.
     *
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *   path="/api/game/preview/{game_id}",
     *   tags={"Game (Match2)"},
     *   security={ {"bearer": {}} },
     *      @OA\Parameter(
     *          name="game_id",
     *          description="id de la partida",
     *          in="path",
     *          style="form"
     *      ),
     *   @OA\Response(response=200, description=""),
     * )
     *
     */
    public function game_preview($game_id)
    {

        // TODO: SEGURIDAD
        /** @var \Leantab\Sherpa\Models\Game $game */
        $game = Sherpa::getGame($game_id);
        $user = User::find(auth()->user()->id);

        $arr = [
            'game_id' => $game->id,
            'name' => $game->name,
            'type' => $game->type,
            'is_goverment' => $game->isGoverment($user->id) ?? false,
            'is_ceo' => $game->isCeo($user->id) ?? false,
            'player_position' => $game->getPlayerPosition($user->id) ?? '',
            'current_stage' => $game->current_stage,
            'created_at' => $game->created_at->format('d/m/Y H:i'),
        ];

        return response()->json($arr);
    }

    /**
     * Retorna información de una partida.
     *
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *   path="/api/game/info/{game_id}",
     *   tags={"Game (Match2)"},
     *   security={ {"bearer": {}} },
     *      @OA\Parameter(
     *          name="game_id",
     *          description="id de la partida",
     *          in="path",
     *          style="form"
     *      ),
     *   @OA\Response(response=200, description=""),
     * )
     *
     */
    public function info($game_id)
    {
        // TODO: SEGURIDAD
        $game = Sherpa::getGame($game_id);
        $res = [
            'match' => $game,
            'ceos' => $game->ceos,
        ];
        return response()->json($res);
    }

    /**
     * Retorna información de una partida.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *   path="/api/game/add_company",
     *   tags={"Game (Match2)"},
     *   security={ {"bearer": {}} },
     *   @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="multipart/form-data",
     *          @OA\Schema(
     *              @OA\Property(property="game_id", type="integer"),
     *              @OA\Property(property="company_name", type="string"),
     *              @OA\Property(property="avatar", type="integer", example="5"),
     *          )
     *      )
     *   ),
     *   @OA\Response(response=200, description=""),
     * )
     *
     */
    public function addCeo(Request $request)
    {
        $request->validate([
            'game_id' => 'required',
            'company_name' => 'required',
            'avatar' => 'required',
        ]);

        $user = User::find(auth()->id());

        Sherpa::addCeo($request->game_id, $user->id, $request->company_name, $request->avatar, true);

        return response()->json(['status' => true]);
    }

    /**
     * Retorna Los parametros para el gobierno en la partida según la etapa del juego
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *   path="/api/game/get_goverment_parameters/{game_id}/{match_step}",
     *   tags={"Game (Match2)"},
     *   security={ {"bearer": {}} },
     *      @OA\Parameter(
     *          name="game_id",
     *          description="INT id de la partida",
     *          in="path",
     *          style="form"
     *      ),
     *      @OA\Parameter(
     *          name="match_step",
     *          description="INT etapa de la partida",
     *          in="path",
     *          style="form"
     *      ),
     *   @OA\Response(response=200, description=""),
     * )
     *
     */
    public function getGovermentParameters($game_id, $match_step)
    {
        return Sherpa::getGovermentParameters($game_id, $match_step);
    }

    /**
     * Guarda las desiciones del gobierno para una etapa de la partida
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *   path="/api/game/set_goverment_parameters",
     *   tags={"Game (Match2)"},
     *   security={ {"bearer": {}} },
     *   @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="multipart/form-data",
     *          @OA\Schema(
     *              @OA\Property(property="game_id", type="integer", example="1"),
     *              @OA\Property(property="parameters", type="string", example="json"),
     *          )
     *      )
     *   ),
     *   @OA\Response(response=200, description=""),
     * )
     *
     */
    public function setGovermentParameters(Request $request)
    {
        return Sherpa::setGovermentParameters($request->input('game_id'), $request->input('parameters'));
    }

    /**
     * Retorna Los parametros para el ceo en la partida según la etapa del juego
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *   path="/api/game/get_ceo_parameters/{game_id}/{match_step}",
     *   tags={"Game (Match2)"},
     *   security={ {"bearer": {}} },
     *      @OA\Parameter(
     *          name="game_id",
     *          description="INT id de la partida",
     *          in="path",
     *          style="form"
     *      ),
     *      @OA\Parameter(
     *          name="match_step",
     *          description="INT etapa de la partida",
     *          in="path",
     *          style="form"
     *      ),
     *   @OA\Response(response=200, description=""),
     * )
     *
     */
    public function getCeoParameters($game_id)
    {
        return Sherpa::getCeoVariables($game_id, auth()->user()->id);
    }

    /**
     * Guarda las desiciones del ceo para una etapa de la partida
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *   path="/api/game/set_ceo_parameters",
     *   tags={"Game (Match2)"},
     *   security={ {"bearer": {}} },
     *   @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="multipart/form-data",
     *          @OA\Schema(
     *              @OA\Property(property="game_id", type="integer", example="1"),
     *              @OA\Property(property="parameters", type="string", example="json"),
     *          )
     *      )
     *   ),
     *   @OA\Response(response=200, description=""),
     * )
     *
     */
    public function setCeoParameters(Request $request)
    {
        return Sherpa::setCeoParameters($request->input('game_id'), $request->input('parameters'), auth()->user()->id);
    }

    /**
     * Retorna el ranking de la partida para una etapa
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *   path="/api/game/get_match_ranking/{game_id}/{match_step}",
     *   tags={"Game (Match2)"},
     *   security={ {"bearer": {}} },
     *      @OA\Parameter(
     *          name="game_id",
     *          description="INT id de la partida",
     *          in="path",
     *          style="form"
     *      ),
     *      @OA\Parameter(
     *          name="match_step",
     *          description="INT etapa de la partida",
     *          in="path",
     *          style="form"
     *      ),
     *   @OA\Response(response=200, description=""),
     * )
     *
     */
    public function getGamehRanking($game_id, $match_step)
    {
        return Sherpa::getGamehRanking($game_id, $match_step);
    }

    /**
     * Eliminar una partida
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *   path="/api/game/delete_game",
     *   tags={"Game (Match2)"},
     *   security={ {"bearer": {}} },
     *   @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="multipart/form-data",
     *          @OA\Schema(
     *              @OA\Property(property="game_id", type="integer", example="1"),
     *          )
     *      )
     *   ),
     *   @OA\Response(response=200, description=""),
     * )
     *
     */
    public function deleteGame(Request $request)
    {
        $this->validate($request, [
            'game_id' => 'required|integer',
        ]);

        return Sherpa::deleteGame($request->game_id);
    }

    /**
     * Eliminar una partida
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *   path="/api/game/delete_ceo",
     *   tags={"Game (Match2)"},
     *   security={ {"bearer": {}} },
     *   @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="multipart/form-data",
     *          @OA\Schema(
     *              @OA\Property(property="game_id", type="integer", example="1"),
     *              @OA\Property(property="user_id", type="integer", example="1"),
     *          )
     *      )
     *   ),
     *   @OA\Response(response=200, description=""),
     * )
     *
     */
    public function deleteCeo(Request $request)
    {
        $this->validate($request, [
            'game_id' => 'required|integer',
            'user_id' => 'required|integer',
        ]);

        return Sherpa::deleteCeo($request->game_id, $request->user_id);
    }

    /**
     * Procesar el turno actual de una partida
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *   path="/api/game/process_game",
     *   tags={"Game (Match2)"},
     *   security={ {"bearer": {}} },
     *   @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="multipart/form-data",
     *          @OA\Schema(
     *              @OA\Property(property="game_id", type="integer", example="1"),
     *          )
     *      )
     *   ),
     *   @OA\Response(response=200, description=""),
     * )
     *
     */
    public function processGame($game_id)
    {
        return Sherpa::processGame($game_id);
    }

    /**
     * Reprocesar un turno de una partida
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *   path="/api/game/reprocess_game",
     *   tags={"Game (Match2)"},
     *   security={ {"bearer": {}} },
     *   @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="multipart/form-data",
     *          @OA\Schema(
     *              @OA\Property(property="game_id", type="integer", example="1"),
     *              @OA\Property(property="stage", type="integer", example="2", description="Etapa de la partida"),
     *          )
     *      )
     *   ),
     *   @OA\Response(response=200, description=""),
     * )
     *
     */
    public function reprocessGame($game_id, $stage)
    {
        return Sherpa::reprocessGame($game_id, $stage);
    }

    /**
     * Forzar el procesar el turno actual de una partida
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *   path="/api/game/force_process_game",
     *   tags={"Game (Match2)"},
     *   security={ {"bearer": {}} },
     *   @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="multipart/form-data",
     *          @OA\Schema(
     *              @OA\Property(property="game_id", type="integer", example="1"),
     *          )
     *      )
     *   ),
     *   @OA\Response(response=200, description=""),
     * )
     *
     */
    public function forceProcessGame($game_id)
    {
        return Sherpa::forceProcessGame($game_id);
    }
}
