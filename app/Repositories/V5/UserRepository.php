<?php

namespace App\Repositories\V5;

use App\Models\V5\User;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class UserRepository extends BaseRepository
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function findByEmail($email)
    {
        return $this->model->where('email', strtolower($email))->first();
    }

    public function findWithRelations($id, array $relations = [])
    {
        $query = $this->model->where('id', $id);

        if (!empty($relations)) {
            $query->with($relations);
        }

        return $query->first();
    }

    public function findAndCountAll(array $options = []): array
    {
        $query = $this->model->query();

        // Map old $conditions format to $options format for backward compatibility
        $conditions = $options;

        // Handle search term
        if (isset($conditions['search_term'])) {
            $searchTerm = $conditions['search_term'];
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'ilike', "%{$searchTerm}%")
                  ->orWhere('email', 'ilike', "%{$searchTerm}%");
            });
        }

        // Handle role filter
        if (isset($conditions['role'])) {
            $userIds = DB::table('user_roles')
                ->where('role_id', $conditions['role'])
                ->pluck('user_id')
                ->toArray();

            if (!empty($userIds)) {
                $query->whereIn('id', $userIds);
            } else {
                return ['rows' => collect([]), 'count' => 0];
            }
        }

        // Handle club filter
        if (isset($conditions['club_id'])) {
            $roleIds = isset($conditions['role']) ? [$conditions['role']] : [5, 6, 7, 8, 9, 11];

            $userIds = DB::table('user_roles')
                ->where('club_id', $conditions['club_id'])
                ->whereIn('role_id', $roleIds)
                ->pluck('user_id')
                ->toArray();

            if (!empty($userIds)) {
                $query->whereIn('id', $userIds);
            } else {
                return ['rows' => collect([]), 'count' => 0];
            }
        }

        // Handle ordering
        $orderBy = $conditions['order_by'] ?? 'id';
        $orderDirection = $conditions['order_direction'] ?? 'ASC';
        $query->orderBy($orderBy, $orderDirection);

        // Handle pagination
        $limit = $conditions['limit'] ?? 20;
        $page = $conditions['page'] ?? 1;
        $offset = ($page - 1) * $limit;

        $count = $query->count();
        $rows = $query->with('roles')
            ->select('id', 'email', 'name', 'disable_emails', 'gender',
                     'birth_year', 'birth_month', 'birth_day', 'nationality',
                     'address_line1', 'address_line2', 'postal_code', 'city',
                     'country', 'phone_numbers', 'debtor_number', 'latlng', 'is_verified')
            ->offset($offset)
            ->limit($limit)
            ->get();

        return ['rows' => $rows, 'count' => $count];
    }

    public function updateByCondition(array $condition, array $data)
    {
        return $this->model->where($condition)->update($data);
    }
}

