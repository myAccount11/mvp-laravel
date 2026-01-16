<?php

namespace App\Services\V5;

use App\Models\V5\Tournament;

interface TournamentStructureGeneratorInterface
{
    /**
     * Generate the tournament structure based on the tournament type
     * 
     * @param Tournament $tournament
     * @param array $settings Structure-specific settings
     * @return void
     */
    public function generateStructure(Tournament $tournament, array $settings): void;

    /**
     * Validate settings for this tournament structure
     * 
     * @param array $settings
     * @return array ['valid' => bool, 'errors' => array]
     */
    public function validateSettings(array $settings): array;
}

