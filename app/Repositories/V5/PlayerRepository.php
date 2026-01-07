<?php

namespace App\Repositories\V5;

use App\Models\V5\Player;
use App\Repositories\BaseRepository;

class PlayerRepository extends BaseRepository
{
    public function __construct(Player $model)
    {
        parent::__construct($model);
    }
}

