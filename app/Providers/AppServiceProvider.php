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
use App\Repositories\V5\TournamentGroupRepository;
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
use App\Repositories\V5\TournamentConfigRepository;
use App\Repositories\V5\RegionRepository;
use App\Repositories\V5\ReservationTypeRepository;
use App\Repositories\V5\UserSeasonSportRepository;
use App\Repositories\V5\RegistrationRepository;
use App\Repositories\V5\CourtPriorityRepository;
use App\Repositories\V5\CoachHistoryRepository;
use App\Repositories\V5\CoachEducationRepository;
use App\Repositories\V5\TournamentStructureRepository;
use App\Repositories\V5\TournamentTypeRepository;
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
use App\Models\V5\TournamentGroup;
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
use App\Models\V5\TournamentConfig;
use App\Models\V5\Region;
use App\Models\V5\ReservationType;
use App\Models\V5\UserSeasonSport;
use App\Models\V5\Registration;
use App\Models\V5\CourtPriority;
use App\Models\V5\CoachHistory;
use App\Models\V5\CoachEducation;
use App\Models\V5\TournamentStructure;
use App\Models\V5\TournamentType;
use App\Models\V5\TournamentProgram;
use App\Models\V5\TournamentRegistrationType;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register Repositories
        $this->app->singleton(OrganizerRepository::class, function ($app) {
            return new OrganizerRepository($app->make(Organizer::class));
        });

        $this->app->singleton(SportRepository::class, function ($app) {
            return new SportRepository($app->make(Sport::class));
        });

        $this->app->singleton(SeasonRepository::class, function ($app) {
            return new SeasonRepository($app->make(Season::class));
        });

        $this->app->singleton(CourtRepository::class, function ($app) {
            return new CourtRepository($app->make(Court::class));
        });

        $this->app->singleton(CourtUsageRepository::class, function ($app) {
            return new CourtUsageRepository($app->make(CourtUsage::class));
        });

        $this->app->singleton(VenueRepository::class, function ($app) {
            return new VenueRepository($app->make(Venue::class));
        });

        $this->app->singleton(PersonRepository::class, function ($app) {
            return new PersonRepository($app->make(Person::class));
        });

        $this->app->singleton(CoachRepository::class, function ($app) {
            return new CoachRepository($app->make(Coach::class));
        });

        $this->app->singleton(PlayerRepository::class, function ($app) {
            return new PlayerRepository($app->make(Player::class));
        });

        $this->app->singleton(RefereeRepository::class, function ($app) {
            return new RefereeRepository($app->make(Referee::class));
        });

        $this->app->singleton(MessageRepository::class, function ($app) {
            return new MessageRepository($app->make(Message::class));
        });

        $this->app->singleton(ReservationRepository::class, function ($app) {
            return new ReservationRepository($app->make(Reservation::class));
        });

        $this->app->singleton(TimeSlotRepository::class, function ($app) {
            return new TimeSlotRepository($app->make(TimeSlot::class));
        });

        $this->app->singleton(ConflictRepository::class, function ($app) {
            return new ConflictRepository($app->make(Conflict::class));
        });

        $this->app->singleton(SuggestionRepository::class, function ($app) {
            return new SuggestionRepository($app->make(Suggestion::class));
        });

        $this->app->singleton(TournamentRepository::class, function ($app) {
            return new TournamentRepository($app->make(Tournament::class));
        });

        $this->app->singleton(PoolRepository::class, function ($app) {
            return new PoolRepository($app->make(Pool::class));
        });

        $this->app->singleton(RoundRepository::class, function ($app) {
            return new RoundRepository($app->make(Round::class));
        });

        $this->app->singleton(LeagueRepository::class, function ($app) {
            return new LeagueRepository($app->make(League::class));
        });

        $this->app->singleton(TournamentGroupRepository::class, function ($app) {
            return new TournamentGroupRepository($app->make(TournamentGroup::class));
        });

        $this->app->singleton(SystemRepository::class, function ($app) {
            return new SystemRepository($app->make(System::class));
        });

        $this->app->singleton(SeasonSportRepository::class, function ($app) {
            return new SeasonSportRepository($app->make(SeasonSport::class));
        });

        $this->app->singleton(PlayerLicenseRepository::class, function ($app) {
            return new PlayerLicenseRepository($app->make(PlayerLicense::class));
        });

        $this->app->singleton(CoachLicenseRepository::class, function ($app) {
            return new CoachLicenseRepository($app->make(CoachLicense::class));
        });

        $this->app->singleton(GameRepository::class, function ($app) {
            return new GameRepository($app->make(Game::class));
        });

        $this->app->singleton(GamePlanRepository::class, function ($app) {
            return new GamePlanRepository($app->make(GamePlan::class));
        });

        $this->app->singleton(GamePenaltyRepository::class, function ($app) {
            return new GamePenaltyRepository($app->make(GamePenalty::class));
        });

        $this->app->singleton(GameNoteRepository::class, function ($app) {
            return new GameNoteRepository($app->make(GameNote::class));
        });

        $this->app->singleton(GameDraftRepository::class, function ($app) {
            return new GameDraftRepository($app->make(GameDraft::class));
        });

        $this->app->singleton(BlockedPeriodRepository::class, function ($app) {
            return new BlockedPeriodRepository($app->make(BlockedPeriod::class));
        });

        $this->app->singleton(TournamentConfigRepository::class, function ($app) {
            return new TournamentConfigRepository($app->make(TournamentConfig::class));
        });

        $this->app->singleton(RegionRepository::class, function ($app) {
            return new RegionRepository($app->make(Region::class));
        });

        $this->app->singleton(ReservationTypeRepository::class, function ($app) {
            return new ReservationTypeRepository($app->make(ReservationType::class));
        });

        $this->app->singleton(UserSeasonSportRepository::class, function ($app) {
            return new UserSeasonSportRepository($app->make(UserSeasonSport::class));
        });

        $this->app->singleton(RegistrationRepository::class, function ($app) {
            return new RegistrationRepository($app->make(Registration::class));
        });

        $this->app->singleton(CourtPriorityRepository::class, function ($app) {
            return new CourtPriorityRepository($app->make(CourtPriority::class));
        });

        $this->app->singleton(CoachHistoryRepository::class, function ($app) {
            return new CoachHistoryRepository($app->make(CoachHistory::class));
        });

        $this->app->singleton(CoachEducationRepository::class, function ($app) {
            return new CoachEducationRepository($app->make(CoachEducation::class));
        });

        $this->app->singleton(TournamentStructureRepository::class, function ($app) {
            return new TournamentStructureRepository($app->make(TournamentStructure::class));
        });

        $this->app->singleton(TournamentTypeRepository::class, function ($app) {
            return new TournamentTypeRepository($app->make(TournamentType::class));
        });

        $this->app->singleton(TournamentProgramRepository::class, function ($app) {
            return new TournamentProgramRepository($app->make(TournamentProgram::class));
        });

        $this->app->singleton(TournamentRegistrationTypeRepository::class, function ($app) {
            return new TournamentRegistrationTypeRepository($app->make(TournamentRegistrationType::class));
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
