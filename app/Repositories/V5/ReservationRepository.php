<?php

namespace App\Repositories\V5;

use App\Models\V5\Reservation;
use App\Repositories\BaseRepository;

class ReservationRepository extends BaseRepository
{
    public function __construct(Reservation $model)
    {
        parent::__construct($model);
    }
}

