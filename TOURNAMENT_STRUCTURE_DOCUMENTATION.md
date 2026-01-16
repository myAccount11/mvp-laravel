# Tournament Structure Backend Documentation

## Overview

This document describes the backend implementation for tournament structure management. The system supports three types of tournament structures:

1. **Regular League** - All teams play with each other a specified number of times
2. **Playoff** - Knockout tournament with random pairings, winners advance
3. **Group Stage + Playoff** - Teams divided into groups, top teams advance to playoff bracket

## Database Structure

### Tables

#### `tournament_configs`
Stores structure-specific settings as JSON for flexibility.

- `id` - Primary key
- `tournament_id` - Foreign key to tournaments (unique)
- `settings` - JSON field containing structure-specific settings
- `created_at`, `updated_at`

#### `games` (updated)
The `games` table is used for both individual games and tournament bracket matches. Additional columns added for bracket structure:

- `round_number` - Round number in bracket (1 = first round, nullable)
- `round_name` - Round name (e.g., "Round of 16", "Quarter-Final", nullable)
- `match_number` - Match number within the round (nullable)
- `position` - Position in bracket (for ordering matches, nullable)
- `games_between` - Number of games to play between teams in this match (nullable, default: 1)
- `home_wins`, `away_wins` - Win counts for the match (nullable, default: 0)
- `parent_match_1_id`, `parent_match_2_id` - Parent matches (self-referential foreign keys to games table, for bracket structure)
- `group_position` - Position in group (1 = first, 2 = second, etc., nullable)
- `is_final` - Boolean flag for final match (default: false)

Existing columns used:
- `tournament_id` - Foreign key to tournaments
- `round_id` - Foreign key to rounds
- `team_id_home`, `team_id_away` - Teams in the match
- `team_id_winner` - Winner of the match
- `status_id` - Game/match status
- `group_id` - For group stage + playoff (which group feeds into match)
- `is_deleted` - Soft delete flag

#### `tournament_groups`
Stores groups for group stage tournaments.

- `id` - Primary key
- `tournament_id` - Foreign key to tournaments
- `name` - Group name (e.g., "Group A")
- `group_number` - Group number for ordering
- `teams_count` - Number of teams in group
- `games_between` - Number of games between teams in group stage
- `advancing_teams_count` - Number of teams that advance to playoff (default: 2)
- `is_deleted` - Soft delete flag

#### `team_tournament_groups`
Pivot table for teams in groups (group stage standings).

- `id` - Primary key
- `group_id` - Foreign key to tournament_groups
- `team_id` - Foreign key to teams
- `position` - Current position in group
- `points`, `wins`, `losses`, `draws` - Statistics
- `goals_for`, `goals_against`, `goal_difference` - Goal statistics

## Models

### TournamentConfig
- `tournament()` - Belongs to Tournament
- `getSetting($key, $default)` - Get a specific setting value
- `setSetting($key, $value)` - Set a specific setting value

### Game (updated)
The `Game` model is used for both individual games and tournament bracket matches. Additional relationships and fields:

- `tournament()`, `round()`, `homeTeam()`, `guestTeam()`, `winnerTeam()`
- `parentMatch1()`, `parentMatch2()` - Parent matches in bracket (self-referential)
- `childMatches()` - Child matches that feed from this match
- `tournamentGroup()` - For group stage + playoff

**Note:** Tournament bracket matches are identified by having `round_number` set (not null). Regular games have `round_number` as null.

### TournamentGroup
- `tournament()` - Belongs to Tournament
- `teams()` - Teams in the group (with pivot standings)
- `matches()` - Matches in this group

## Service Classes

### TournamentStructureGeneratorInterface
Interface that all structure generators must implement:
- `generateStructure(Tournament $tournament, array $settings): void`
- `validateSettings(array $settings): array`

### RegularLeagueStructureService
Generates regular league structure:
- Creates a single pool
- Assigns all teams to the pool
- Settings:
  - `teams_count` (required, min: 2)
  - `games_between` (required, min: 1)

### PlayoffStructureService
Generates playoff bracket:
- Randomly seeds teams
- Creates bracket matches with parent-child relationships
- Settings:
  - `teams_count` (required, must be power of 2: 2, 4, 8, 16, 32, etc.)
  - `games_between` (required, min: 1) - Games in each match except final
  - `final_games_between` (optional, min: 1) - Games in final match

### GroupStageAndPlayoffStructureService
Generates group stage + playoff:
- Divides teams into groups randomly
- Creates group stage structure
- Creates playoff bracket for advancing teams
- Settings:
  - `groups_count` (required, min: 2)
  - `teams_per_group` (required, min: 2)
  - `playoff_teams_count` (required, must be power of 2)
  - `games_between_in_group_stage` (required, min: 1)
  - `games_between_in_playoff_stage` (required, min: 1)
  - `games_between_in_final` (optional, min: 1)

### TournamentStructureService
Main service that routes to appropriate generator:
- `generateStructure(Tournament $tournament, array $settings): void`
- `validateSettings(string $structureValue, array $settings): array`
- `getDefaultSettings(string $structureValue): array`

## API Endpoints

### POST `/api/v5/tournaments/{id}/generate-structure`
Generate tournament structure.

**Request Body:**
```json
{
  "settings": {
    "teams_count": 16,
    "games_between": 2,
    "final_games_between": 1
  }
}
```

**Response:**
```json
{
  "message": "Tournament structure generated successfully",
  "tournament": { ... }
}
```

### GET `/api/v5/tournaments/structure/default-settings?structure_value=playoffs`
Get default settings for a structure type.

**Response:**
```json
{
  "settings": {
    "teams_count": 16,
    "games_between": 1,
    "final_games_between": 1
  }
}
```

### POST `/api/v5/tournaments/structure/validate-settings`
Validate settings for a structure.

**Request Body:**
```json
{
  "structure_value": "playoffs",
  "settings": {
    "teams_count": 16,
    "games_between": 2
  }
}
```

**Response:**
```json
{
  "valid": true,
  "errors": []
}
```

## Usage Examples

### Regular League
```php
$settings = [
    'teams_count' => 8,
    'games_between' => 2
];

$structureService->generateStructure($tournament, $settings);
```

### Playoff
```php
$settings = [
    'teams_count' => 16,
    'games_between' => 1,
    'final_games_between' => 1
];

$structureService->generateStructure($tournament, $settings);
```

### Group Stage + Playoff
```php
$settings = [
    'groups_count' => 4,
    'teams_per_group' => 4,
    'playoff_teams_count' => 8,
    'games_between_in_group_stage' => 1,
    'games_between_in_playoff_stage' => 1,
    'games_between_in_final' => 1
];

$structureService->generateStructure($tournament, $settings);
```

## Extending the System

To add a new tournament structure:

1. Create a new service class implementing `TournamentStructureGeneratorInterface`
2. Add the structure to `TournamentStructure` model constants
3. Update `TournamentStructureService::getGenerator()` to route to your new service
4. Add default settings in `TournamentStructureService::getDefaultSettings()`

Example:
```php
// In TournamentStructureService
protected function getGenerator(string $structureValue): TournamentStructureGeneratorInterface
{
    return match ($structureValue) {
        'regular_league' => $this->regularLeagueService,
        'playoffs' => $this->playoffService,
        'group_stage_and_playoffs' => $this->groupStageAndPlayoffService,
        'new_structure' => $this->newStructureService, // Add here
        default => throw new \InvalidArgumentException("Unknown tournament structure: {$structureValue}"),
    };
}
```

## Notes

- All structure generation happens within database transactions
- Teams must be added to tournament before generating structure
- Team counts are validated before structure generation
- Playoff structures require team counts to be powers of 2
- Group stage + playoff requires total teams = groups_count × teams_per_group
- Playoff brackets are automatically linked with parent-child relationships
- Group stage standings are tracked in `team_tournament_groups` pivot table

