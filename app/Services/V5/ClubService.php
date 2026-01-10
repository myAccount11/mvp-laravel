<?php

namespace App\Services\V5;

use App\Repositories\V5\ClubRepository;
use App\Services\V5\UserService;
use App\Services\V5\MessageService;
use Illuminate\Support\Facades\DB;
use App\Models\V5\User;

class ClubService
{
    protected $clubRepository;
    protected $userService;
    protected $messageService;

    public function __construct(
        ClubRepository $clubRepository,
        UserService $userService,
        MessageService $messageService
    ) {
        $this->clubRepository = $clubRepository;
        $this->userService = $userService;
        $this->messageService = $messageService;
    }

    public function findAndCountAll(array $conditions = [])
    {
        return $this->clubRepository->findAndCountAll($conditions);
    }

    public function findAll(array $conditions = [])
    {
        $query = $this->clubRepository->query();

        if (isset($conditions['where'])) {
            $query->where($conditions['where']);
        }

        if (isset($conditions['include'])) {
            foreach ($conditions['include'] as $include) {
                $query->with($include);
            }
        }

        return $query->get();
    }

    public function findOne(array $conditions, array $relations = [])
    {
        return $this->clubRepository->findOneBy($conditions, ['*'], $relations);
    }

    public function createClub(array $data, $seasonSportId)
    {
        $lastLicense = $this->clubRepository->getLastLicense();
        $license = $lastLicense + 1;
        $club = $this->clubRepository->create(array_merge($data, ['license' => $license]));

        DB::table('club_season_sports')->insert([
            'club_id' => $club->id,
            'season_sport_id' => $seasonSportId,
        ]);

        return $club;
    }

    public function getClubWithVenuesAndCourts($clubId)
    {
        return $this->clubRepository->findWithVenuesAndCourts($clubId);
    }

    public function addClubUser($userEmail, $clubId, $seasonSportId, User $authUser)
    {
        $club = $this->clubRepository->find($clubId);
        $user = $this->userService->findOne(['email' => $userEmail]);

        if (!$user) {
            $user = $this->userService->createUser([
                'name' => explode('@', $userEmail)[0],
                'email' => $userEmail,
                'disable_emails' => false,
                'password' => '',
            ]);

            $this->messageService->create([
                'type_id' => 1,
                'to_id' => 0,
                'html' => "You are invited to MVP by <strong>{$authUser->name}</strong> from the association <strong>{$club->name}</strong><br /><br />With the MVP App you can see your match schedule, follow your favorites, watch live scoring and much more<br /><br />Download it in the App Store or Google Play<br /><br />With Kind Regards MVP App",
                'email' => $userEmail,
                'user_id' => 1473,
            ]);

            DB::table('user_season_sports')->insert([
                'user_id' => $user->id,
                'season_sport_id' => $seasonSportId,
            ]);

            DB::table('user_roles')->insert([
                'user_id' => $user->id,
                'role_id' => 10,
                'club_id' => $clubId,
                'season_sport_id' => $seasonSportId,
                'user_role_approved_by_user_id' => $authUser->id,
            ]);
        }

        return $user;
    }

    public function update($id, array $data)
    {
        return $this->clubRepository->update($id, $data);
    }

    public function delete($id)
    {
        return $this->clubRepository->delete($id);
    }
}

