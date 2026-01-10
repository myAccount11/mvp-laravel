<?php

namespace App\Repositories\V5;

use App\Models\V5\Club;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class ClubRepository extends BaseRepository
{
    public function __construct(Club $model)
    {
        parent::__construct($model);
    }

    public function findAndCountAll(array $options = []): array
    {
        $query = $this->model->query();

        // Map old $conditions format to $options format for backward compatibility
        $conditions = $options;

        if (isset($conditions['search_term'])) {
            $searchTerm = $conditions['search_term'];
            if (is_numeric($searchTerm)) {
                $query->where('license', $searchTerm);
            } else {
                $query->where(function($q) use ($searchTerm) {
                    $q->where('name', 'ilike', "%{$searchTerm}%")
                      ->orWhere('short_name', 'ilike', "%{$searchTerm}%");
                });
            }
        }

        if (isset($conditions['club_ids'])) {
            $clubIds = is_array($conditions['club_ids']) 
                ? $conditions['club_ids'] 
                : json_decode($conditions['club_ids'], true);
            if (is_array($clubIds) && count($clubIds) > 0) {
                $query->whereIn('id', $clubIds);
            }
        }

        if (isset($conditions['season_sport_id'])) {
            $query->whereHas('clubSeasonSports', function($q) use ($conditions) {
                $q->where('season_sport_id', $conditions['season_sport_id']);
            });
        }

        $orderBy = $conditions['order_by'] ?? 'id';
        $orderDirection = $conditions['order_direction'] ?? 'ASC';
        $query->orderBy($orderBy, $orderDirection);

        $limit = $conditions['limit'] ?? 20;
        $page = $conditions['page'] ?? 1;
        $offset = ($page - 1) * $limit;

        $count = $query->count();
        $rows = $query->with(['clubSeasonSports', 'clubVenues', 'managers'])
            ->offset($offset)
            ->limit($limit)
            ->get();

        return ['rows' => $rows, 'count' => $count];
    }

    public function getLastLicense()
    {
        return $this->model->orderBy('license', 'desc')->value('license') ?? 60000000;
    }

    public function findWithVenuesAndCourts($id)
    {
        return $this->model->with([
            'clubVenues.venue.courts' => function($q) use ($id) {
                $q->with(['courtPriorities' => function($q2) use ($id) {
                    $q2->where('club_id', $id)->orderBy('court_priority_number', 'ASC');
                }]);
            }
        ])->find($id);
    }
}

