<?php

namespace App\Repositories\V5;

use App\Models\V5\Suggestion;
use App\Repositories\BaseRepository;

class SuggestionRepository extends BaseRepository
{
    public function __construct(Suggestion $model)
    {
        parent::__construct($model);
    }
}

