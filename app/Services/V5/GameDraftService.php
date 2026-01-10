<?php

namespace App\Services\V5;

use App\Models\V5\GameDraft;
use App\Repositories\V5\GameDraftRepository;

class GameDraftService
{
    protected GameDraftRepository $gameDraftRepository;

    public function __construct(GameDraftRepository $gameDraftRepository)
    {
        $this->gameDraftRepository = $gameDraftRepository;
    }

    public function findOne(array $condition): ?GameDraft
    {
        return $this->gameDraftRepository->findOneBy($condition);
    }

    public function findAll(array $condition = []): \Illuminate\Database\Eloquent\Collection
    {
        return $this->gameDraftRepository->findBy($condition);
    }

    public function create(array $data): GameDraft
    {
        return $this->gameDraftRepository->create($data);
    }

    public function createMany(array $data): array
    {
        $inserted = $this->gameDraftRepository->query()->insert($data);
        // insert() returns bool, but we need to return array
        // Return empty array on success, or throw exception on failure
        if ($inserted) {
            return [];
        }
        throw new \Exception('Failed to insert game drafts');
    }

    public function deleteByCondition(array $condition): int
    {
        return $this->gameDraftRepository->query()->where($condition)->delete();
    }

    public function update(int $id, array $data): bool
    {
        return $this->gameDraftRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->gameDraftRepository->delete($id);
    }
}

