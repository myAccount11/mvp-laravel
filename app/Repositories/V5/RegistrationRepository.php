<?php

namespace App\Repositories\V5;

use App\Models\V5\Registration;
use App\Repositories\BaseRepository;

class RegistrationRepository extends BaseRepository
{
    public function __construct(Registration $model)
    {
        parent::__construct($model);
    }
}

