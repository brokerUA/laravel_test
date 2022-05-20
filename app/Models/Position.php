<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\hasMany;

/**
 * @property string $title
 *
 * @property Collection $users
 */
class Position extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title'
    ];

    /**
     * Get users that owns the position.
     */
    public function users(): hasMany
    {
        return $this->hasMany(User::class);
    }
}
