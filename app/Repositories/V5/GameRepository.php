<?php

namespace App\Repositories\V5;

use App\Models\V5\Game;
use App\Repositories\BaseRepository;

class GameRepository extends BaseRepository
{
    public function __construct(Game $model)
    {
        parent::__construct($model);
    }
}

