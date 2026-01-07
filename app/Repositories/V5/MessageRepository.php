<?php

namespace App\Repositories\V5;

use App\Models\V5\Message;
use App\Repositories\BaseRepository;

class MessageRepository extends BaseRepository
{
    public function __construct(Message $model)
    {
        parent::__construct($model);
    }
}

