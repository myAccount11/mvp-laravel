<?php

namespace App\Services\V5;

use App\Models\V5\System;
use App\Repositories\V5\SystemRepository;

class SystemService
{
    public function __construct(protected SystemRepository $systemRepository)
    {
    }

    public function findOne(array $condition): ?System
    {
        return $this->systemRepository->findOneBy($condition);
    }

    public function update(int $id, array $data): bool
    {
        return $this->systemRepository->update($id, $data);
    }
}

