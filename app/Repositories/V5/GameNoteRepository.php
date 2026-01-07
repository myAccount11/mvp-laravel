<?php

namespace App\Repositories\V5;

use App\Models\V5\GameNote;
use App\Repositories\BaseRepository;

class GameNoteRepository extends BaseRepository
{
    public function __construct(GameNote $model)
    {
        parent::__construct($model);
    }
}

