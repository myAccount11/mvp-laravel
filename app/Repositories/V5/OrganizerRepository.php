<?php

namespace App\Repositories\V5;

use App\Models\V5\Organizer;
use App\Repositories\BaseRepository;

class OrganizerRepository extends BaseRepository
{
    public function __construct(Organizer $model)
    {
        parent::__construct($model);
    }
}

