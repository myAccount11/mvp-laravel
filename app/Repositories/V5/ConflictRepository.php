<?php

namespace App\Repositories\V5;

use App\Models\V5\Conflict;
use App\Repositories\BaseRepository;

class ConflictRepository extends BaseRepository
{
    public function __construct(Conflict $model)
    {
        parent::__construct($model);
    }
}

