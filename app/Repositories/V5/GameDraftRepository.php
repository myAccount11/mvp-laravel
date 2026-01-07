<?php

namespace App\Repositories\V5;

use App\Models\V5\GameDraft;
use App\Repositories\BaseRepository;

class GameDraftRepository extends BaseRepository
{
    public function __construct(GameDraft $model)
    {
        parent::__construct($model);
    }
}

