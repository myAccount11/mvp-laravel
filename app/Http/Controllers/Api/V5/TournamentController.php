<?php

namespace App\Http\Controllers\Api\V5;

use App\Http\Controllers\Controller;
use App\Http\Requests\V5\CreateTournamentRequest;
use App\Http\Requests\V5\UpdateTournamentRequest;
use App\Services\V5\TournamentService;
use App\Services\V5\TournamentStructureService;
use App\Models\V5\TournamentConfig;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TournamentController extends Controller
{
    protected TournamentService $tournamentService;
    protected TournamentStructureService $structureService;

    public function __construct(
        TournamentService $tournamentService,
        TournamentStructureService $structureService
    ) {
        $this->tournamentService = $tournamentService;
        $this->structureService = $structureService;
    }

    public function create(CreateTournamentRequest $request): JsonResponse
    {
        $data = $request->validated();
        $structureSettings = $data['structure_settings'] ?? null;
        unset($data['structure_settings']);

        $tournament = DB::transaction(function () use ($data, $structureSettings) {
            $tournament = $this->tournamentService->create($data);

            // Create tournament config if structure_settings provided
            if ($structureSettings && $tournament->tournament_structure_id) {
                TournamentConfig::create([
                    'tournament_id' => $tournament->id,
                    'settings' => $structureSettings,
                ]);
            }

            return $tournament;
        });

        $tournament->load(['region', 'league', 'tournamentStructure', 'tournamentConfig']);
        return response()->json($tournament, 201);
    }

    public function getAll(Request $request): JsonResponse
    {
        $orderBy = $request->query('order_by', 'id');
        $orderDirection = $request->query('order_direction', 'ASC');
        $page = (int)$request->query('page', 1);
        $limit = (int)$request->query('limit', 20);
        $searchTerm = $request->query('search_term');
        $leagueId = $request->query('league_id');

        $conditions = [
            'orderBy' => $orderBy,
            'orderDirection' => $orderDirection,
            'page' => $page,
            'limit' => $limit,
            'include' => ['region', 'league'],
        ];

        if ($searchTerm) {
            $conditions['searchTerm'] = $searchTerm;
        }

        if ($leagueId && $leagueId !== '' && $leagueId !== '0') {
            $conditions['leagueId'] = (int)$leagueId;
        }

        $result = $this->tournamentService->findAndCountAll($conditions);

        return response()->json([
            'rows' => $result['rows'],
            'count' => $result['count']
        ]);
    }

    public function getNames(Request $request): JsonResponse
    {
        $queryParams = $request->all();
        $tournaments = $this->tournamentService->findAll($queryParams);
        return response()->json($tournaments);
    }

    public function getTeamsByTournamentId(int $id): JsonResponse
    {
        $tournament = $this->tournamentService->findOne([
            'where' => ['id' => $id],
            'include' => ['teams', 'league'],
        ]);

        if (!$tournament) {
            return response()->json(['message' => 'Tournament not found'], 404);
        }

        return response()->json($tournament);
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $deleted = $this->tournamentService->delete($id);
            if (!$deleted) {
                return response()->json(['message' => 'Tournament not found'], 404);
            }
            return response()->json(['message' => 'Tournament deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function getById(int $id): JsonResponse
    {
        $tournament = $this->tournamentService->findOne(['id' => $id]);
        if (!$tournament) {
            return response()->json(['message' => 'Tournament not found'], 404);
        }

        $tournament->load([
            'region',
            'league',
            'tournamentStructure',
            'tournamentConfig',
            'tournamentGroups' => function ($q) {
                $q->orderBy('group_number')
                  ->with([
                      'teams' => function ($tq) {
                          $tq->orderBy('pivot_position');
                      },
                      'matches' => function ($mq) {
                          $mq->orderBy('match_number')
                            ->with(['homeTeam', 'guestTeam', 'winnerTeam']);
                      }
                  ]);
            },
            'tournamentMatches' => function ($q) {
                $q->orderBy('round_number')->orderBy('match_number')
                  ->with(['homeTeam', 'guestTeam', 'winnerTeam']);
            },
            'rounds' => function ($q) {
                $q->orderBy('id', 'ASC');
            },
            'teams'
        ]);

        return response()->json($tournament);
    }

    public function update(int $id, UpdateTournamentRequest $request): JsonResponse
    {
        $data = $request->validated();
        $structureSettings = $data['structure_settings'] ?? null;
        unset($data['structure_settings']);

        DB::transaction(function () use ($id, $data, $structureSettings, &$updated) {
            $updated = $this->tournamentService->update($id, $data);

            // Update or create tournament config if structure_settings provided
            if ($structureSettings !== null) {
                $tournament = $this->tournamentService->findOne(['id' => $id]);
                if ($tournament) {
                    TournamentConfig::updateOrCreate(
                        ['tournament_id' => $tournament->id],
                        ['settings' => $structureSettings]
                    );
                }
            }
        });

        if (!$updated) {
            return response()->json(['message' => 'Tournament not found or no changes made'], 404);
        }

        $tournament = $this->tournamentService->findOne(['id' => $id]);
        $tournament->load([
            'region',
            'league',
            'tournamentStructure',
            'tournamentConfig',
            'tournamentGroups' => function ($q) {
                $q->orderBy('group_number')
                  ->with([
                      'teams' => function ($tq) {
                          $tq->orderBy('pivot_position');
                      },
                      'matches' => function ($mq) {
                          $mq->orderBy('match_number')
                            ->with(['homeTeam', 'guestTeam', 'winnerTeam']);
                      }
                  ]);
            },
            'tournamentMatches' => function ($q) {
                $q->orderBy('round_number')->orderBy('match_number')
                  ->with(['homeTeam', 'guestTeam', 'winnerTeam']);
            },
            'rounds' => function ($q) {
                $q->orderBy('id', 'ASC');
            },
            'teams'
        ]);

        return response()->json($tournament);
    }

    public function getPossibleTeamsForTournament(int $id): JsonResponse
    {
        $teams = $this->tournamentService->getPossibleTeamsForTournament($id);
        return response()->json($teams);
    }

    /**
     * Generate tournament structure based on tournament type and settings
     */
    public function generateStructure(int $id, Request $request): JsonResponse
    {
        try {
            $tournament = $this->tournamentService->findOne(['id' => $id]);

            if (!$tournament) {
                return response()->json(['message' => 'Tournament not found'], 404);
            }

            $settings = $request->input('settings', []);

            if (empty($settings)) {
                return response()->json(['message' => 'Settings are required'], 400);
            }

            // Validate settings
            $structure = $tournament->tournamentStructure;
            if (!$structure) {
                return response()->json(['message' => 'Tournament must have a structure'], 400);
            }

            $validation = $this->structureService->validateSettings($structure->value, $settings);
            if (!$validation['valid']) {
                return response()->json([
                    'message' => 'Invalid settings',
                    'errors' => $validation['errors']
                ], 400);
            }

            // Generate structure
            $this->structureService->generateStructure($tournament, $settings);

            // Reload tournament with all relations
            $tournament->refresh();
            $tournament->load([
                'tournamentStructure',
                'tournamentConfig',
                'tournamentGroups' => function ($q) {
                    $q->orderBy('group_number')
                      ->with([
                          'teams' => function ($tq) {
                              $tq->orderBy('pivot_position');
                          },
                          'matches' => function ($mq) {
                              $mq->orderBy('match_number')
                                ->with(['homeTeam', 'guestTeam', 'winnerTeam']);
                          }
                      ]);
                },
                'tournamentMatches' => function ($q) {
                    $q->orderBy('round_number')->orderBy('match_number')
                      ->with(['homeTeam', 'guestTeam', 'winnerTeam']);
                },
                'rounds',
                'teams'
            ]);

            return response()->json([
                'message' => 'Tournament structure generated successfully',
                'tournament' => $tournament
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to generate structure: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get default settings for a tournament structure
     */
    public function getDefaultSettings(Request $request): JsonResponse
    {
        $structureValue = $request->query('structure_value');

        if (!$structureValue) {
            return response()->json(['message' => 'structure_value is required'], 400);
        }

        $settings = $this->structureService->getDefaultSettings($structureValue);

        return response()->json(['settings' => $settings]);
    }

    /**
     * Validate tournament structure settings
     */
    public function validateStructureSettings(Request $request): JsonResponse
    {
        $structureValue = $request->input('structure_value');
        $settings = $request->input('settings', []);

        if (!$structureValue) {
            return response()->json(['message' => 'structure_value is required'], 400);
        }

        $validation = $this->structureService->validateSettings($structureValue, $settings);

        return response()->json($validation);
    }
}

