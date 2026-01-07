<?php

namespace App\Repositories\V5;

use App\Models\V5\TournamentRegistrationType;
use App\Repositories\BaseRepository;

class TournamentRegistrationTypeRepository extends BaseRepository
{
    public function __construct(TournamentRegistrationType $model)
    {
        parent::__construct($model);
    }
}

