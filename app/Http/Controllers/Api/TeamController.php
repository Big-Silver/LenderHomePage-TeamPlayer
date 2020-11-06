<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\PermissionResource;
use App\Http\Resources\TeamResource;
use App\Laravue\JsonResponse;
use App\Laravue\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Validator;

/**
 * Class TeamController
 *
 * @package App\Http\Controllers\Api
 */
class TeamController extends BaseController
{
    const ITEM_PER_PAGE = 15;

    /**
     * Display a listing of the team resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response|ResourceCollection
     */
    public function index(Request $request)
    {
        $searchParams = $request->all();
        $teamQuery = Team::query();
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        $keyword = Arr::get($searchParams, 'keyword', '');

        if (!empty($keyword)) {
            $teamQuery->where('first_name', 'LIKE', '%' . $keyword . '%');
            $teamQuery->where('last_name', 'LIKE', '%' . $keyword . '%');
            $teamQuery->where('email', 'LIKE', '%' . $keyword . '%');
        }

        return TeamResource::collection($teamQuery->paginate($limit));
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
            $team = Team::create([
                'name' => $params['name'],
            ]);

            return new TeamResource($team);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  Team $team
     * @return TeamResource|\Illuminate\Http\JsonResponse
     */
    public function show(Team $team)
    {
        return new TeamResource($team);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param team    $team
     * @return TeamResource|\Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Team $team)
    {
        if ($team === null) {
            return response()->json(['error' => 'Team not found'], 404);
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
            $name = $request->get('name');
            $found = Team::where('name', $name)->first();
            if ($found && $found->id !== $team->id) {
                return response()->json(['error' => 'Team name has been taken'], 403);
            }

            $team->name = $name;
            $team->save();
            return new TeamResource($team);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Team $team
     * @return \Illuminate\Http\Response
     */
    public function destroy(Team $team)
    {
        try {
            $team->delete();
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
            'name' => 'required'
        ];
    }
}
