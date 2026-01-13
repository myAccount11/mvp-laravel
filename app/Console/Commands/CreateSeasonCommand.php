<?php

namespace App\Console\Commands;

use App\Models\V5\Season;
use App\Models\V5\SeasonSport;
use App\Models\V5\Sport;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateSeasonCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'season:create
                            {--year= : Starting year (defaults to current year)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new season with format current_year/next_year and attach to all sports';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $year = $this->option('year') ?? date('Y');
        $nextYear = (int) $year + 1;
        $seasonName = "{$year}/{$nextYear}";

        $this->info("Creating/updating season: {$seasonName}");

        try {
            DB::beginTransaction();

            // Create or update the season
            $season = Season::updateOrCreate(
                ['name' => $seasonName],
                ['name' => $seasonName]
            );

            // Get all sports
            $sports = Sport::all();

            if ($sports->isEmpty()) {
                $this->warn('No sports found in the database. Season created but no sports attached.');
                DB::commit();
                return Command::SUCCESS;
            }

            $this->newLine();
            $this->info("Attaching season to all sports...");

            $attachedCount = 0;
            $skippedCount = 0;

            foreach ($sports as $sport) {
                // Check if season-sport relationship already exists
                $existingSeasonSport = SeasonSport::where('season_id', $season->id)
                    ->where('sport_id', $sport->id)
                    ->first();

                if ($existingSeasonSport) {
                    $this->line("  - Sport '{$sport->name}' already attached. Skipping...");
                    $skippedCount++;
                    continue;
                }

                // Create season-sport relationship
                SeasonSport::create([
                    'season_id' => $season->id,
                    'sport_id' => $sport->id,
                ]);

                $this->info("  ✓ Attached sport: {$sport->name}");
            }

            DB::commit();
            return Command::SUCCESS;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Failed to create season: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
