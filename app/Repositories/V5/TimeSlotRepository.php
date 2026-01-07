<?php

namespace App\Repositories\V5;

use App\Models\V5\TimeSlot;
use App\Repositories\BaseRepository;

class TimeSlotRepository extends BaseRepository
{
    public function __construct(TimeSlot $model)
    {
        parent::__construct($model);
    }
}

