<?php

namespace App\Repositories\V5;

use App\Models\V5\CoachEducation;
use App\Repositories\BaseRepository;

class CoachEducationRepository extends BaseRepository
{
    public function __construct(CoachEducation $model)
    {
        parent::__construct($model);
    }
}

