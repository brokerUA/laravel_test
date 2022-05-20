<?php

namespace App\Http\Services;

use App\Models\Position;
use Illuminate\Database\Eloquent\Collection;

class PositionService
{
    /**
     * @var Position
     */
    private $model;

    /**
     * UserService constructor.
     * @param Position $model
     */
    public function __construct(Position $model)
    {
        $this->model = $model;
    }

    /**
     * @return Collection
     */
    public function getAll()
    {
        return $this->model::all();
    }
}
