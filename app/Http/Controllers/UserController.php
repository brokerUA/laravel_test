<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApiStoreUserRequest;
use App\Http\Requests\ApiIndexUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Services\PositionService;
use App\Http\Services\UserService;
use App\Http\Resources\{
    UserResource, UserCollection
};
use Illuminate\Http\{JsonResponse, Request};
use Illuminate\Support\Facades\{App, View};

class UserController extends Controller
{
    private $serviceUser;

    private $servicePosition;

    public function __construct(UserService $serviceUser, PositionService $servicePosition)
    {
        $this->serviceUser = $serviceUser;

        $this->servicePosition = $servicePosition;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function index(Request $request)
    {
        $usersPaginator = $this->serviceUser->buildWithPaginate();

        $positions = $this->servicePosition->getAll();

        return View::make(
            'users.index',
            compact('usersPaginator', 'positions')
        );
    }

    /**
     * Display a listing of the resource.
     *
     * @param ApiIndexUserRequest $request
     *
     * @return mixed
     */
    public function indexAPI(ApiIndexUserRequest $request)
    {
        $users = $this->serviceUser->buildIndexApi($request);

        if ($users->count() === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Page not found',
            ], 404);
        }

        $count = $request->get('count', config('app.count_per_page'));

        $usersCount = $this->serviceUser->getUsersCount();

        $additional = [
            'success' => true,
            'links' => $this->serviceUser->buildUrls($request),
            'page' => $request->get('page', 1),
            'total_pages' => ceil($usersCount / $count),
            'total_users' => $usersCount,
            'count' => $count,
        ];

        return (new UserCollection($users))
            ->additional($additional);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreUserRequest  $request
     * @return mixed
     */
    public function store(StoreUserRequest $request)
    {
        $user = $this->serviceUser->save($request);

        if (!App::environment('local')) {
            $this->serviceUser->addImageJob($user);
        }

        return redirect()->route('users');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  ApiStoreUserRequest  $request
     * @return JsonResponse
     */
    public function storeAPI(ApiStoreUserRequest $request): JsonResponse
    {
        if ($this->serviceUser->isEmailOrPhoneExists($request)) {
            return response()->json([
                'success' => false,
                'message' => 'User with this phone or email already exist',
            ], '409');
        }

        $user = $this->serviceUser->save($request);

        if (!App::environment('local')) {
            $this->serviceUser->addImageJob($user);
        }

        return response()->json([
            "success" => true,
            "user_id" => $user->id,
            "message" => "New user successfully registered"
        ], '200');
    }

    /**
     * Display the specified resource.
     * @param  string $user_id
     * @return UserResource|JsonResponse
     */
    public function showAPI(string $user_id)
    {
        if (!ctype_digit($user_id)) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'fails' => collect([
                    'user_id' => [
                        "The user_id must be an integer."
                    ]
                ])
            ], 400);
        }

        $user = $this->serviceUser->findUser($user_id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'The user with the requested identifier does not exist',
                'fails' => collect([
                    'user_id' => [
                        "User not found"
                    ]
                ])
            ], 404);
        }

        return (new UserResource($user))
            ->additional(['success' => true]);
    }
}
