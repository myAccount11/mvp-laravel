<?php

namespace App\Repositories\V5;

use App\Models\V5\CoachLicense;
use App\Repositories\BaseRepository;

class CoachLicenseRepository extends BaseRepository
{
    public function __construct(CoachLicense $model)
    {
        parent::__construct($model);
    }
}

