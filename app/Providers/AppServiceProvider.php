<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// Repositories
use App\Repositories\V5\OrganizerRepository;
use App\Repositories\V5\SportRepository;
use App\Repositories\V5\SeasonRepository;
use App\Repositories\V5\CourtRepository;
use App\Repositories\V5\CourtUsageRepository;
use App\Repositories\V5\VenueRepository;
use App\Repositories\V5\PersonRepository;
use App\Repositories\V5\CoachRepository;
use App\Repositories\V5\PlayerRepository;
use App\Repositories\V5\RefereeRepository;
use App\Repositories\V5\MessageRepository;
use App\Repositories\V5\ReservationRepository;
use App\Repositories\V5\TimeSlotRepository;
use App\Repositories\V5\ConflictRepository;
use App\Repositories\V5\SuggestionRepository;
use App\Repositories\V5\TournamentRepository;
use App\Repositories\V5\PoolRepository;
use App\Repositories\V5\RoundRepository;
use App\Repositories\V5\LeagueRepository;
use App\Repositories\V5\SystemRepository;
use App\Repositories\V5\SeasonSportRepository;
use App\Repositories\V5\PlayerLicenseRepository;
use App\Repositories\V5\CoachLicenseRepository;
use App\Repositories\V5\GameRepository;
use App\Repositories\V5\GamePlanRepository;
use App\Repositories\V5\GamePenaltyRepository;
use App\Repositories\V5\GameNoteRepository;
use App\Repositories\V5\GameDraftRepository;
use App\Repositories\V5\BlockedPeriodRepository;
use App\Repositories\V5\RegionRepository;
use App\Repositories\V5\ReservationTypeRepository;
use App\Repositories\V5\UserSeasonSportRepository;
use App\Repositories\V5\RegistrationRepository;
use App\Repositories\V5\CourtPriorityRepository;
use App\Repositories\V5\CoachHistoryRepository;
use App\Repositories\V5\CoachEducationRepository;
use App\Repositories\V5\TournamentStructureRepository;
use App\Repositories\V5\TournamentProgramRepository;
use App\Repositories\V5\TournamentRegistrationTypeRepository;

// Models
use App\Models\V5\Organizer;
use App\Models\V5\Sport;
use App\Models\V5\Season;
use App\Models\V5\Court;
use App\Models\V5\CourtUsage;
use App\Models\V5\Venue;
use App\Models\V5\Person;
use App\Models\V5\Coach;
use App\Models\V5\Player;
use App\Models\V5\Referee;
use App\Models\V5\Message;
use App\Models\V5\Reservation;
use App\Models\V5\TimeSlot;
use App\Models\V5\Conflict;
use App\Models\V5\Suggestion;
use App\Models\V5\Tournament;
use App\Models\V5\Pool;
use App\Models\V5\Round;
use App\Models\V5\League;
use App\Models\V5\System;
use App\Models\V5\SeasonSport;
use App\Models\V5\PlayerLicense;
use App\Models\V5\CoachLicense;
use App\Models\V5\Game;
use App\Models\V5\GamePlan;
use App\Models\V5\GamePenalty;
use App\Models\V5\GameNote;
use App\Models\V5\GameDraft;
use App\Models\V5\BlockedPeriod;
use App\Models\V5\Region;
use App\Models\V5\ReservationType;
use App\Models\V5\UserSeasonSport;
use App\Models\V5\Registration;
use App\Models\V5\CourtPriority;
use App\Models\V5\CoachHistory;
use App\Models\V5\CoachEducation;
use App\Models\V5\TournamentStructure;
use App\Models\V5\TournamentProgram;
use App\Models\V5\TournamentRegistrationType;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register Repositories (using direct instantiation to avoid circular dependencies)
        $this->app->bind(OrganizerRepository::class, function () {
            return new OrganizerRepository(new Organizer());
        });

        $this->app->bind(SportRepository::class, function () {
            return new SportRepository(new Sport());
        });

        $this->app->bind(SeasonRepository::class, function () {
            return new SeasonRepository(new Season());
        });

        $this->app->bind(CourtRepository::class, function () {
            return new CourtRepository(new Court());
        });

        $this->app->bind(CourtUsageRepository::class, function () {
            return new CourtUsageRepository(new CourtUsage());
        });

        $this->app->bind(VenueRepository::class, function () {
            return new VenueRepository(new Venue());
        });

        $this->app->bind(PersonRepository::class, function () {
            return new PersonRepository(new Person());
        });

        $this->app->bind(CoachRepository::class, function () {
            return new CoachRepository(new Coach());
        });

        $this->app->bind(PlayerRepository::class, function () {
            return new PlayerRepository(new Player());
        });

        $this->app->bind(RefereeRepository::class, function () {
            return new RefereeRepository(new Referee());
        });

        $this->app->bind(MessageRepository::class, function () {
            return new MessageRepository(new Message());
        });

        $this->app->bind(ReservationRepository::class, function () {
            return new ReservationRepository(new Reservation());
        });

        $this->app->bind(TimeSlotRepository::class, function () {
            return new TimeSlotRepository(new TimeSlot());
        });

        $this->app->bind(ConflictRepository::class, function () {
            return new ConflictRepository(new Conflict());
        });

        $this->app->bind(SuggestionRepository::class, function () {
            return new SuggestionRepository(new Suggestion());
        });

        $this->app->bind(TournamentRepository::class, function () {
            return new TournamentRepository(new Tournament());
        });

        $this->app->bind(PoolRepository::class, function () {
            return new PoolRepository(new Pool());
        });

        $this->app->bind(RoundRepository::class, function () {
            return new RoundRepository(new Round());
        });

        $this->app->bind(LeagueRepository::class, function () {
            return new LeagueRepository(new League());
        });


        $this->app->bind(SystemRepository::class, function () {
            return new SystemRepository(new System());
        });

        $this->app->bind(SeasonSportRepository::class, function () {
            return new SeasonSportRepository(new SeasonSport());
        });

        $this->app->bind(PlayerLicenseRepository::class, function () {
            return new PlayerLicenseRepository(new PlayerLicense());
        });

        $this->app->bind(CoachLicenseRepository::class, function () {
            return new CoachLicenseRepository(new CoachLicense());
        });

        $this->app->bind(GameRepository::class, function () {
            return new GameRepository(new Game());
        });

        $this->app->bind(GamePlanRepository::class, function () {
            return new GamePlanRepository(new GamePlan());
        });

        $this->app->bind(GamePenaltyRepository::class, function () {
            return new GamePenaltyRepository(new GamePenalty());
        });

        $this->app->bind(GameNoteRepository::class, function () {
            return new GameNoteRepository(new GameNote());
        });

        $this->app->bind(GameDraftRepository::class, function () {
            return new GameDraftRepository(new GameDraft());
        });

        $this->app->bind(BlockedPeriodRepository::class, function () {
            return new BlockedPeriodRepository(new BlockedPeriod());
        });


        $this->app->bind(RegionRepository::class, function () {
            return new RegionRepository(new Region());
        });

        $this->app->bind(ReservationTypeRepository::class, function () {
            return new ReservationTypeRepository(new ReservationType());
        });

        $this->app->bind(UserSeasonSportRepository::class, function () {
            return new UserSeasonSportRepository(new UserSeasonSport());
        });

        $this->app->bind(RegistrationRepository::class, function () {
            return new RegistrationRepository(new Registration());
        });

        $this->app->bind(CourtPriorityRepository::class, function () {
            return new CourtPriorityRepository(new CourtPriority());
        });

        $this->app->bind(CoachHistoryRepository::class, function () {
            return new CoachHistoryRepository(new CoachHistory());
        });

        $this->app->bind(CoachEducationRepository::class, function () {
            return new CoachEducationRepository(new CoachEducation());
        });

        $this->app->bind(TournamentStructureRepository::class, function () {
            return new TournamentStructureRepository(new TournamentStructure());
        });

        $this->app->bind(TournamentProgramRepository::class, function () {
            return new TournamentProgramRepository(new TournamentProgram());
        });

        $this->app->bind(TournamentRegistrationTypeRepository::class, function () {
            return new TournamentRegistrationTypeRepository(new TournamentRegistrationType());
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
