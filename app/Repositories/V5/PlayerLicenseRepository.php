<?php

namespace App\Repositories\V5;

use App\Models\V5\PlayerLicense;
use App\Repositories\BaseRepository;

class PlayerLicenseRepository extends BaseRepository
{
    public function __construct(PlayerLicense $model)
    {
        parent::__construct($model);
    }
}

