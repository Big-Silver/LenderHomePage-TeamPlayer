<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\PermissionResource;
use App\Http\Resources\PlayerResource;
use App\Laravue\JsonResponse;
use App\Laravue\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Validator;

/**
 * Class PlayerController
 *
 * @package App\Http\Controllers\Api
 */
class PlayerController extends BaseController
{
    const ITEM_PER_PAGE = 15;

    /**
     * Display a listing of the player resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response|ResourceCollection
     */
    public function index(Request $request)
    {
        $searchParams = $request->all();
        $playerQuery = Player::query();
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        $keyword = Arr::get($searchParams, 'keyword', '');

        if (!empty($keyword)) {
            $playerQuery->where('first_name', 'LIKE', '%' . $keyword . '%');
            $playerQuery->where('last_name', 'LIKE', '%' . $keyword . '%');
            $playerQuery->where('email', 'LIKE', '%' . $keyword . '%');
        }

        return PlayerResource::collection($playerQuery->paginate($limit));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            array_merge(
                $this->getValidationRules(),
            )
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 403);
        } else {
            $params = $request->all();
            $player = Player::create([
                'first_name' => $params['first_name'],
                'last_name' => $params['last_name'],
                'email' => $params['email'],
                'team' => $params['team'],
            ]);

            return new PlayerResource($player);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  Player $player
     * @return PlayerResource|\Illuminate\Http\JsonResponse
     */
    public function show(Player $player)
    {
        return new PlayerResource($player);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Player    $player
     * @return PlayerResource|\Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Player $player)
    {
        if ($player === null) {
            return response()->json(['error' => 'Player not found'], 404);
        }

        $currentUser = Auth::user();
        if (!$currentUser->isAdmin()
            && $currentUser->id !== $user->id
            && !$currentUser->hasPermission(\App\Laravue\Acl::PERMISSION_USER_MANAGE)
        ) {
            return response()->json(['error' => 'Permission denied'], 403);
        }

        $validator = Validator::make($request->all(), $this->getValidationRules(false));
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 403);
        } else {
            $email = $request->get('email');
            $found = Player::where('email', $email)->first();
            if ($found && $found->id !== $player->id) {
                return response()->json(['error' => 'Email has been taken'], 403);
            }

            $player->first_name = $request->get('first_name');
            $player->last_name = $request->get('last_name');
            $player->email = $email;
            $player->team = $request->get('team');
            $player->save();
            return new PlayerResource($player);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Player $player
     * @return \Illuminate\Http\Response
     */
    public function destroy(Player $player)
    {
        try {
            $player->delete();
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 403);
        }

        return response()->json(null, 204);
    }

    /**
     * @param bool $isNew
     * @return array
     */
    private function getValidationRules($isNew = true)
    {
        return [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => $isNew ? 'required|email|unique:players' : 'required|email',
            'team' => 'required'
        ];
    }
}
