<?php

namespace App\Services\V5;

use App\Models\V5\Tournament;

class TournamentStructureService
{
    protected RegularLeagueStructureService $regularLeagueService;
    protected PlayoffStructureService $playoffService;
    protected GroupStageAndPlayoffStructureService $groupStageAndPlayoffService;

    public function __construct(
        RegularLeagueStructureService $regularLeagueService,
        PlayoffStructureService $playoffService,
        GroupStageAndPlayoffStructureService $groupStageAndPlayoffService
    ) {
        $this->regularLeagueService = $regularLeagueService;
        $this->playoffService = $playoffService;
        $this->groupStageAndPlayoffService = $groupStageAndPlayoffService;
    }

    /**
     * Generate tournament structure based on tournament type
     */
    public function generateStructure(Tournament $tournament, array $settings): void
    {
        $structure = $tournament->tournamentStructure;

        if (!$structure) {
            throw new \InvalidArgumentException('Tournament must have a tournament structure');
        }

        $generator = $this->getGenerator($structure->value);
        $generator->generateStructure($tournament, $settings);
    }

    /**
     * Validate settings for a tournament structure
     */
    public function validateSettings(string $structureValue, array $settings): array
    {
        $generator = $this->getGenerator($structureValue);
        return $generator->validateSettings($settings);
    }

    /**
     * Get the appropriate generator for the structure type
     */
    protected function getGenerator(string $structureValue): TournamentStructureGeneratorInterface
    {
        return match ($structureValue) {
            'regular_league' => $this->regularLeagueService,
            'playoffs' => $this->playoffService,
            'group_stage_and_playoffs' => $this->groupStageAndPlayoffService,
            default => throw new \InvalidArgumentException("Unknown tournament structure: {$structureValue}"),
        };
    }

    /**
     * Get default settings for a tournament structure
     */
    public function getDefaultSettings(string $structureValue): array
    {
        return match ($structureValue) {
            'regular_league' => [
                'teams_count' => 8,
                'games_between' => 1,
            ],
            'playoffs' => [
                'teams_count' => 16,
                'games_between' => 1,
                'final_games_between' => 1,
            ],
            'group_stage_and_playoffs' => [
                'groups_count' => 4,
                'teams_per_group' => 4,
                'playoff_teams_count' => 8,
                'games_between_in_group_stage' => 1,
                'games_between_in_playoff_stage' => 1,
                'games_between_in_final' => 1,
            ],
            default => [],
        };
    }
}

