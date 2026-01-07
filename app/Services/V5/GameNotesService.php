<?php

namespace App\Services\V5;

use App\Models\V5\GameNote;
use App\Repositories\V5\GameNoteRepository;

class GameNotesService
{
    protected GameNoteRepository $gameNoteRepository;

    public function __construct(GameNoteRepository $gameNoteRepository)
    {
        $this->gameNoteRepository = $gameNoteRepository;
    }

    public function findOne(array $condition): ?GameNote
    {
        return $this->gameNoteRepository->findOneBy($condition);
    }

    public function create(array $data): GameNote
    {
        return $this->gameNoteRepository->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->gameNoteRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->gameNoteRepository->delete($id);
    }
}

