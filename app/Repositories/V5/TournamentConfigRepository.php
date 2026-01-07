<?php

namespace App\Repositories\V5;

use App\Models\V5\TournamentConfig;
use App\Repositories\BaseRepository;

class TournamentConfigRepository extends BaseRepository
{
    public function __construct(TournamentConfig $model)
    {
        parent::__construct($model);
    }
}

