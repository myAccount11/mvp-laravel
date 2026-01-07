<?php

namespace App\Repositories\V5;

use App\Models\V5\Venue;
use App\Repositories\BaseRepository;

class VenueRepository extends BaseRepository
{
    public function __construct(Venue $model)
    {
        parent::__construct($model);
    }
}

