<?php

namespace App\Services\V5;

use App\Models\V5\UserSeasonSport;
use App\Models\V5\SeasonSport;
use App\Repositories\V5\UserSeasonSportRepository;
use Exception;

class UserSeasonSportsService
{
    public function __construct(protected UserSeasonSportRepository $userSeasonSportRepository)
    {
    }

    public function findOne(array $condition): ?UserSeasonSport
    {
        return $this->userSeasonSportRepository->findOneBy($condition);
    }

    public function findAll(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->userSeasonSportRepository->all();
    }

    /**
     * Find all user season sports with optional filters and includes.
     */
    public function findAllWithFilters($userId = null, $include = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = UserSeasonSport::query();

        if ($userId) {
            $query->where('user_id', $userId);
        }

        if ($include) {
            // Parse include parameter (can be comma-separated string or array)
            $relations = is_array($include) ? $include : explode(',', $include);
            // Convert dot notation to proper relation format (e.g., seasonSport.sport)
            $query->with($relations);
        }

        return $query->get();
    }

    public function create(array $data): UserSeasonSport
    {
        return $this->userSeasonSportRepository->create($data);
    }

    /**
     * Create a user season sport by finding the newest season for the given sport_id.
     *
     * @param array $data Must contain 'user_id' and 'sport_id'
     * @return UserSeasonSport
     * @throws Exception
     */
    public function createWithSportId(array $data): UserSeasonSport
    {
        $sportId = $data['sport_id'] ?? null;
        $userId = $data['user_id'] ?? null;

        if (!$sportId) {
            throw new Exception('sport_id is required');
        }

        if (!$userId) {
            throw new Exception('user_id is required');
        }

        // Find the newest season_sport for this sport
        // Join with seasons table and order by season name descending (e.g., 2026/2027 > 2025/2026)
        $seasonSport = SeasonSport::where('sport_id', $sportId)
            ->join('seasons', 'season_sports.season_id', '=', 'seasons.id')
            ->orderBy('seasons.name', 'desc')
            ->select('season_sports.*')
            ->first();

        if (!$seasonSport) {
            throw new Exception('No season found for the selected sport');
        }

        // Check if user already has this season_sport
        $existing = $this->userSeasonSportRepository->findOneBy([
            'user_id' => $userId,
            'season_sport_id' => $seasonSport->id,
        ]);

        if ($existing) {
            return $existing;
        }

        // Create the user season sport
        return $this->userSeasonSportRepository->create([
            'user_id' => $userId,
            'season_sport_id' => $seasonSport->id,
            'is_active' => $data['is_active'] ?? true,
        ]);
    }

    public function update(int $id, array $data): bool
    {
        return $this->userSeasonSportRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->userSeasonSportRepository->delete($id);
    }

    /**
     * Get user's existing season sports and latest season sports for their sports.
     *
     * @param int $userId
     * @return array ['existing' => [...], 'latest' => [...]]
     */
    public function getExistingAndLatestSeasonSports(int $userId): array
    {
        // Get user's existing season sports with relations
        $existingUserSeasonSports = UserSeasonSport::where('user_id', $userId)
            ->where('is_active', true)
            ->with(['seasonSport.season', 'seasonSport.sport'])
            ->get();

        // Extract existing season sports
        $existingSeasonSports = $existingUserSeasonSports->map(function ($userSeasonSport) {
            return $userSeasonSport->seasonSport;
        })->filter();

        // Get unique sport IDs from user's existing season sports
        $sportIds = $existingSeasonSports->pluck('sport_id')->unique()->toArray();

        // Get latest season sport for each sport
        $latestSeasonSports = collect();

        foreach ($sportIds as $sportId) {
            // Find the latest season_sport for this sport
            // Order by season name descending (e.g., 2026/2027 > 2025/2026)
            $latestSeasonSport = SeasonSport::where('sport_id', $sportId)
                ->join('seasons', 'season_sports.season_id', '=', 'seasons.id')
                ->orderBy('seasons.name', 'desc')
                ->select('season_sports.*')
                ->with(['season', 'sport'])
                ->first();

            if ($latestSeasonSport) {
                // Check if user already has this season_sport
                $userHasThis = $existingUserSeasonSports->contains(function ($userSeasonSport) use ($latestSeasonSport) {
                    return $userSeasonSport->season_sport_id === $latestSeasonSport->id;
                });

                // Only include if user doesn't already have it
                if (!$userHasThis) {
                    $latestSeasonSports->push($latestSeasonSport);
                }
            }
        }

        return [
            'existing' => $existingSeasonSports->values()->all(),
            'latest' => $latestSeasonSports->values()->all(),
        ];
    }
}

