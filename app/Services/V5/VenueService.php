<?php

namespace App\Services\V5;

use App\Models\V5\Venue;
use App\Repositories\V5\VenueRepository;
use App\Services\V5\ClubService;
use App\Services\V5\CourtService;
use App\Models\V5\VenueSeasonSport;
use App\Models\V5\ClubVenue;
use App\Models\V5\Court;
use App\Models\V5\Club;

class VenueService
{
    protected VenueRepository $venueRepository;
    protected ClubService $clubService;
    protected CourtService $courtService;

    public function __construct(
        VenueRepository $venueRepository,
        ClubService $clubService,
        CourtService $courtService
    ) {
        $this->venueRepository = $venueRepository;
        $this->clubService = $clubService;
        $this->courtService = $courtService;
    }

    public function createVenue(array $dto, int $seasonSportId): Venue
    {
        $venue = $this->venueRepository->create($dto);

        VenueSeasonSport::create([
            'venue_id' => $venue->id,
            'season_sport_id' => $seasonSportId,
        ]);

        return $venue;
    }

    public function findAndCountAll(array $conditions = []): array
    {
        $orderBy = $conditions['orderBy'] ?? 'id';
        $orderDirection = $conditions['orderDirection'] ?? 'ASC';
        $page = $conditions['page'] ?? 1;
        $limit = $conditions['limit'] ?? 20;
        $searchTerm = $conditions['searchTerm'] ?? null;
        $seasonSportId = $conditions['seasonSportId'] ?? null;

        $query = $this->venueRepository->query();

        if ($searchTerm) {
            $query->where('name', 'ILIKE', "%{$searchTerm}%");
        }

        $query->with([
            'courts' => function ($q) {
                $q->orderBy('id', 'ASC');
            },
            'clubVenues.club' => function ($q) {
                $q->orderBy('id', 'ASC');
            },
            'venueSeasonSports' => function ($q) use ($seasonSportId) {
                if ($seasonSportId) {
                    $q->where('season_sport_id', $seasonSportId);
                }
            },
        ]);

        if ($seasonSportId) {
            $query->whereHas('venueSeasonSports', function ($q) use ($seasonSportId) {
                $q->where('season_sport_id', $seasonSportId);
            });
        }

        $count = $query->count();
        $query->orderBy($orderBy, $orderDirection);
        $query->limit($limit);
        $query->offset(($page - 1) * $limit);

        $rows = $query->get();

        return ['rows' => $rows, 'count' => $count];
    }

    public function findAll(array $conditions = []): \Illuminate\Database\Eloquent\Collection
    {
        $query = $this->venueRepository->query();

        if (isset($conditions['include'])) {
            $query->with($conditions['include']);
        }

        if (isset($conditions['order'])) {
            foreach ($conditions['order'] as $order) {
                if (is_array($order) && count($order) >= 2) {
                    $query->orderBy($order[0], $order[1] ?? 'ASC');
                }
            }
        }

        return $query->get();
    }

    public function findOne(array $condition): ?Venue
    {
        return $this->venueRepository->findOneBy($condition);
    }

    public function updateVenue(int $id, array $updateVenueDto): array
    {
        try {
            $venue = $this->venueRepository->query()->with('clubVenues')->find($id);

            if (!$venue) {
                return ['message' => 'Venue not found'];
            }

            $deletedLicenseNumbers = $updateVenueDto['deleted_license_numbers'] ?? [];
            $clubLicenseNumbers = $updateVenueDto['club_license_numbers'] ?? [];
            $courtsCreate = $updateVenueDto['courts_create'] ?? [];
            $courtsUpdate = $updateVenueDto['courts_update'] ?? [];
            $deleteCourts = $updateVenueDto['delete_courts'] ?? [];

            $theOthers = collect($updateVenueDto)->except([
                'deleted_license_numbers',
                'club_license_numbers',
                'courts_create',
                'courts_update',
                'delete_courts',
            ])->toArray();

            $this->venueRepository->update($id, $theOthers);

            // Add club associations
            if (!empty($clubLicenseNumbers)) {
                foreach ($clubLicenseNumbers as $clubLicense) {
                    $club = $this->clubService->findOne(['license' => $clubLicense]);

                    if (!$club) {
                        return ['message' => 'Club not found'];
                    }

                    $existingAssociation = ClubVenue::where('club_id', $club->id)
                        ->where('venue_id', $venue->id)
                        ->first();

                    if ($existingAssociation) {
                        return ['message' => 'Club is already associated with this venue.'];
                    }

                    ClubVenue::create([
                        'club_id' => $club->id,
                        'venue_id' => $venue->id,
                    ]);
                }
            }

            // Remove club associations
            if (!empty($deletedLicenseNumbers)) {
                foreach ($deletedLicenseNumbers as $license) {
                    $club = $this->clubService->findOne(['license' => $license]);

                    if ($club) {
                        $existingAssociation = ClubVenue::where('club_id', $club->id)
                            ->where('venue_id', $venue->id)
                            ->first();

                        if ($existingAssociation) {
                            $existingAssociation->delete();
                        }
                    }
                }
            }

            // Create courts
            if (!empty($courtsCreate)) {
                foreach ($courtsCreate as $court) {
                    $createCourtDto = array_merge($court['create_court_dto'] ?? [], [
                        'venue_id' => $venue->id,
                    ]);
                    $this->courtService->createCourt($createCourtDto, $court['court_requirements'] ?? []);
                }
            }

            // Update courts
            if (!empty($courtsUpdate)) {
                foreach ($courtsUpdate as $court) {
                    $this->courtService->updateCourt(
                        $court['id'],
                        $court['update_court_dto'] ?? [],
                        $court['court_requirements'] ?? []
                    );
                }
            }

            // Delete courts
            if (!empty($deleteCourts)) {
                foreach ($deleteCourts as $courtForDel) {
                    $court = $this->courtService->findOne([
                        'id' => $courtForDel['id'],
                        'venue_id' => $courtForDel['venue_id'],
                    ]);
                    if ($court) {
                        $this->courtService->delete($court->id);
                    }
                }
            }

            return ['message' => 'Success'];
        } catch (\Exception $error) {
            return ['message' => "Error updating venue: {$error->getMessage()}"];
        }
    }

    public function delete(int $id): bool
    {
        return $this->venueRepository->delete($id);
    }
}

