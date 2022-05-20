<?php

namespace App\Http\Services;

use App\Http\Requests\ApiIndexUserRequest;
use App\Http\Requests\ApiStoreUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Jobs\ProcessImage;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserService
{
    /**
     * @var User
     */
    private $model;

    /**
     * @var int
     */
    private $modelCount;

    /**
     * UserService constructor.
     * @param User $model
     */
    public function __construct(User $model)
    {
        $this->model = $model;

        $this->modelCount = $this->model::count();
    }

    /**
     * @param StoreUserRequest|ApiStoreUserRequest $request
     * @return User
     */
    public function save($request): User
    {
        $fileName = '__raw__' . time() . $request->file('photo')->getClientOriginalName();

        return $this->model::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'position_id' => $request->input('position_id'),
            'photo' => $request->file('photo')->storePubliclyAs('photos', $fileName, 'public'),
            'password' => Hash::make('password'),
        ]);
    }

    /**
     * @param ApiStoreUserRequest $request
     * @return bool
     */
    public function isEmailOrPhoneExists(ApiStoreUserRequest $request): bool
    {
        return $this->model::where('email', $request->post('email'))
            ->orWhere('phone', $request->post('phone'))
            ->exists();
    }

    /**
     * @param User $user
     * @return void
     */
    public function addImageJob(User $user): void
    {
        ProcessImage::dispatch($user);
    }

    /**
     * @param int $userID
     * @return ?User
     */
    public function findUser(int $userID): ?User
    {
        return $this->model::find($userID);
    }

    /**
     * @return LengthAwarePaginator
     */
    public function buildWithPaginate(): LengthAwarePaginator
    {
        $queryBuilder = $this->model->newQuery();

        $queryBuilder = $this->scopeSort(
            $queryBuilder,
            'id',
            'asc'
        );

        return $queryBuilder->paginate(
            config('app.count_per_page')
        );
    }

    /**
     * @param ApiIndexUserRequest $request
     * @return Collection
     */
    public function buildIndexApi(ApiIndexUserRequest $request): Collection
    {
        $queryBuilder = $this->model->newQuery();

        $queryBuilder = $this->scopeSort(
            $queryBuilder,
            'id',
            'asc'
        );

        $count = $request->get('count', config('app.count_per_page'));

        $queryBuilder = $this->scopeLimit(
            $queryBuilder,
            $count
        );

        $queryBuilder = $this->scopeOffset(
            $queryBuilder,
            $count,
            $request->get('offset'),
            $request->get('page')
        );

        return $queryBuilder->get();
    }

    /**
     * @return int
     */
    public function getUsersCount(): int
    {
        return $this->modelCount;
    }

    /**
     * @param Builder $query
     * @param int $count
     * @return Builder
     */
    protected function scopeLimit(Builder $query, int $count): Builder
    {
        return $query->limit($count);
    }

    /**
     * @param Builder $query
     * @param string $sort
     * @param string $order
     * @return Builder
     */
    protected function scopeSort(Builder $query, string $sort, string $order): Builder
    {
        return $query->orderBy($sort, $order);
    }

    /**
     * @param Builder $query
     * @param int $count
     * @param ?int $offset
     * @param ?int $page
     * @return Builder
     */
    protected function scopeOffset(Builder $query, int $count, int $offset = null, int $page = null): Builder
    {
        if ($offset) {
            $query = $query->offset($offset);

        } elseif ($page) {
            $query = $query->offset(($page - 1) * $count);

        }

        return $query;
    }

    /**
     * @param ApiIndexUserRequest $request
     * @return array
     */
    public function buildUrls(ApiIndexUserRequest $request): array
    {
        $urls = [
            'next_url' => null,
            'prev_url' => null,
        ];

        $count = $request->get('count', config('app.count_per_page'));

        $usersCount = $this->getUsersCount();

        if ($request->has('offset')) {
            $offset = $request->get('offset');

            if ($usersCount < $offset) {
                $urls['next_url'] = route('usersAPI') . '?offset=' . $offset . '&count=' . $count;
            }
            $urls['prev_url'] = route('usersAPI') . '?page=1&count=' . $count;

        } elseif ($request->has('page')) {
            $page = $request->get('page');

            if ($usersCount > $page * $count) {
                $urls['next_url'] = route('usersAPI') . '?page=' . ($page + 1)  . '&count=' . $count;
            }
            if ($page > 1) {
                $urls['prev_url'] = route('usersAPI') . '?page=' . ($page - 1) . '&count=' . $count;
            }
        } else {
            $urls['next_url'] = route('usersAPI') . '?page=2&count=' . $count;
        }

        return $urls;
    }
}
