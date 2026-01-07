<?php

namespace App\Repositories\V5;

use App\Models\V5\Person;
use App\Repositories\BaseRepository;

class PersonRepository extends BaseRepository
{
    public function __construct(Person $model)
    {
        parent::__construct($model);
    }
}

