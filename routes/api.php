<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\V5\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/validate/email', [AuthController::class, 'validateEmail']);
    Route::get('/verify-email', [AuthController::class, 'verifyEmail']);
    Route::post('/google/sign-in', [AuthController::class, 'googleSignIn']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    Route::post('/forget-password', [AuthController::class, 'forgetPassword']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);
});

// Protected routes
Route::middleware(['jwt.auth'])->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']);
    
    // V5 API routes
    Route::prefix('v5')->group(function () {
        Route::prefix('users')->group(function () {
            Route::get('/', [UserController::class, 'index']);
            Route::post('/', [UserController::class, 'create']);
            Route::post('/create-byadmin', [UserController::class, 'createByAdmin']);
            Route::get('/teams/{id}', [UserController::class, 'getTeamUser']);
            Route::get('/{id}', [UserController::class, 'show']);
            Route::put('/{id}', [UserController::class, 'update']);
            Route::delete('/{id}', [UserController::class, 'destroy']);
            Route::delete('/{id}/roles', [UserController::class, 'deleteRole']);
        });

        Route::prefix('roles')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\V5\RoleController::class, 'getAll']);
            Route::post('/', [\App\Http\Controllers\Api\V5\RoleController::class, 'create']);
            Route::get('/{value}', [\App\Http\Controllers\Api\V5\RoleController::class, 'getByValue']);
            Route::post('/{roleId}/users/{userId}', [\App\Http\Controllers\Api\V5\RoleController::class, 'assignUserRole']);
            Route::delete('/users/{userId}', [\App\Http\Controllers\Api\V5\RoleController::class, 'detachUserRoles']);
            Route::post('/users/{userId}', [\App\Http\Controllers\Api\V5\RoleController::class, 'approveUserRoles']);
        });

        Route::prefix('clubs')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\V5\ClubController::class, 'index']);
            Route::get('/all', [\App\Http\Controllers\Api\V5\ClubController::class, 'getAll']);
            Route::post('/', [\App\Http\Controllers\Api\V5\ClubController::class, 'create']);
            Route::get('/{id}', [\App\Http\Controllers\Api\V5\ClubController::class, 'show']);
            Route::get('/{id}/users', [\App\Http\Controllers\Api\V5\ClubController::class, 'getClubUsers']);
            Route::get('/{id}/users/all', [\App\Http\Controllers\Api\V5\ClubController::class, 'getAllClubUsers']);
            Route::get('/{id}/courts', [\App\Http\Controllers\Api\V5\ClubController::class, 'getByIdCourtsPriority']);
            Route::put('/{id}', [\App\Http\Controllers\Api\V5\ClubController::class, 'update']);
            Route::delete('/{id}', [\App\Http\Controllers\Api\V5\ClubController::class, 'destroy']);
            Route::post('/{id}/users', [\App\Http\Controllers\Api\V5\ClubController::class, 'addClubUser']);
        });

        Route::prefix('teams')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\V5\TeamController::class, 'index']);
            Route::get('/names', [\App\Http\Controllers\Api\V5\TeamController::class, 'getNames']);
            Route::post('/', [\App\Http\Controllers\Api\V5\TeamController::class, 'create']);
            Route::get('/{id}', [\App\Http\Controllers\Api\V5\TeamController::class, 'show']);
            Route::get('/{id}/users', [\App\Http\Controllers\Api\V5\TeamController::class, 'getTeamUsers']);
            Route::put('/{id}', [\App\Http\Controllers\Api\V5\TeamController::class, 'update']);
            Route::delete('/{id}', [\App\Http\Controllers\Api\V5\TeamController::class, 'destroy']);
            Route::post('/{id}/attach-groups', [\App\Http\Controllers\Api\V5\TeamController::class, 'attachGroups']);
            Route::post('/{id}/attach-tournament/{tournamentId}', [\App\Http\Controllers\Api\V5\TeamController::class, 'attachTournament']);
            Route::delete('/tournaments/{teamTournamentId}', [\App\Http\Controllers\Api\V5\TeamController::class, 'removeTeamFromTournament']);
            Route::post('/{id}/add-user', [\App\Http\Controllers\Api\V5\TeamController::class, 'addUserToTeam']);
        });

        Route::prefix('organizers')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\V5\OrganizerController::class, 'getAll']);
            Route::post('/', [\App\Http\Controllers\Api\V5\OrganizerController::class, 'create']);
            Route::get('/{id}', [\App\Http\Controllers\Api\V5\OrganizerController::class, 'getById']);
            Route::put('/{id}', [\App\Http\Controllers\Api\V5\OrganizerController::class, 'update']);
            Route::delete('/{id}', [\App\Http\Controllers\Api\V5\OrganizerController::class, 'destroy']);
        });

        Route::prefix('sports')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\V5\SportController::class, 'getSports']);
            Route::post('/', [\App\Http\Controllers\Api\V5\SportController::class, 'create']);
            Route::get('/{id}', [\App\Http\Controllers\Api\V5\SportController::class, 'getById']);
            Route::put('/{id}', [\App\Http\Controllers\Api\V5\SportController::class, 'update']);
            Route::delete('/{id}', [\App\Http\Controllers\Api\V5\SportController::class, 'remove']);
        });

        Route::prefix('seasons')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\V5\SeasonController::class, 'getAll']);
            Route::post('/', [\App\Http\Controllers\Api\V5\SeasonController::class, 'create']);
            Route::get('/{id}', [\App\Http\Controllers\Api\V5\SeasonController::class, 'getById']);
            Route::put('/{id}', [\App\Http\Controllers\Api\V5\SeasonController::class, 'update']);
            Route::delete('/{id}', [\App\Http\Controllers\Api\V5\SeasonController::class, 'destroy']);
        });

        Route::prefix('courts')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\V5\CourtController::class, 'getAll']);
            Route::get('/for-filter', [\App\Http\Controllers\Api\V5\CourtController::class, 'getCourtsForFilter']);
            Route::get('/for-clubs', [\App\Http\Controllers\Api\V5\CourtController::class, 'getCourtsForClubs']);
            Route::get('/{id}', [\App\Http\Controllers\Api\V5\CourtController::class, 'getById']);
            Route::delete('/{id}', [\App\Http\Controllers\Api\V5\CourtController::class, 'destroy']);
        });

        Route::prefix('venues')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\V5\VenueController::class, 'getVenues']);
            Route::get('/all', [\App\Http\Controllers\Api\V5\VenueController::class, 'getAllVenues']);
            Route::post('/', [\App\Http\Controllers\Api\V5\VenueController::class, 'create']);
            Route::get('/{id}', [\App\Http\Controllers\Api\V5\VenueController::class, 'getById']);
            Route::put('/{id}', [\App\Http\Controllers\Api\V5\VenueController::class, 'update']);
            Route::delete('/{id}', [\App\Http\Controllers\Api\V5\VenueController::class, 'destroy']);
        });

        Route::prefix('persons')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\V5\PersonController::class, 'getAllPersons']);
            Route::post('/', [\App\Http\Controllers\Api\V5\PersonController::class, 'create']);
            Route::put('/{id}', [\App\Http\Controllers\Api\V5\PersonController::class, 'update']);
            Route::put('/player-license', [\App\Http\Controllers\Api\V5\PersonController::class, 'updatePlayerLicense']);
        });

        Route::prefix('coaches')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\V5\CoachController::class, 'getAllCoaches']);
            Route::post('/', [\App\Http\Controllers\Api\V5\CoachController::class, 'create']);
            Route::post('/create-coach', [\App\Http\Controllers\Api\V5\CoachController::class, 'createCoach']);
            Route::post('/coach-info', [\App\Http\Controllers\Api\V5\CoachController::class, 'coachInfo']);
            Route::get('/{id}', [\App\Http\Controllers\Api\V5\CoachController::class, 'getById']);
            Route::delete('/{id}', [\App\Http\Controllers\Api\V5\CoachController::class, 'destroy']);
        });

        Route::prefix('players')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\V5\PlayerController::class, 'getAllPlayers']);
            Route::post('/', [\App\Http\Controllers\Api\V5\PlayerController::class, 'create']);
            Route::put('/{id}/jersey-number', [\App\Http\Controllers\Api\V5\PlayerController::class, 'updateJerseyNumber']);
        });

        Route::prefix('referees')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\V5\RefereeController::class, 'getAllReferees']);
            Route::post('/', [\App\Http\Controllers\Api\V5\RefereeController::class, 'create']);
            Route::get('/{id}', [\App\Http\Controllers\Api\V5\RefereeController::class, 'getRefereeById']);
            Route::delete('/{id}', [\App\Http\Controllers\Api\V5\RefereeController::class, 'destroy']);
        });

        Route::prefix('messages')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\V5\MessageController::class, 'getAll']);
            Route::get('/count', [\App\Http\Controllers\Api\V5\MessageController::class, 'getMessagesCount']);
            Route::get('/counts', [\App\Http\Controllers\Api\V5\MessageController::class, 'getMessagesCount']);
            Route::post('/', [\App\Http\Controllers\Api\V5\MessageController::class, 'create']);
            Route::get('/{id}', [\App\Http\Controllers\Api\V5\MessageController::class, 'getById'])->where('id', '[0-9]+');
            Route::put('/{id}', [\App\Http\Controllers\Api\V5\MessageController::class, 'update'])->where('id', '[0-9]+');
            Route::delete('/{id}', [\App\Http\Controllers\Api\V5\MessageController::class, 'remove'])->where('id', '[0-9]+');
        });

        Route::prefix('reservations')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\V5\ReservationController::class, 'getAll']);
            Route::post('/', [\App\Http\Controllers\Api\V5\ReservationController::class, 'create']);
            Route::get('/{id}', [\App\Http\Controllers\Api\V5\ReservationController::class, 'getById']);
            Route::put('/{id}', [\App\Http\Controllers\Api\V5\ReservationController::class, 'update']);
            Route::delete('/{id}', [\App\Http\Controllers\Api\V5\ReservationController::class, 'destroy']);
        });

        Route::prefix('time-slots')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\V5\TimeSlotController::class, 'getAll']);
            Route::post('/', [\App\Http\Controllers\Api\V5\TimeSlotController::class, 'create']);
            Route::get('/{id}', [\App\Http\Controllers\Api\V5\TimeSlotController::class, 'getById']);
            Route::put('/{id}', [\App\Http\Controllers\Api\V5\TimeSlotController::class, 'update']);
            Route::delete('/{id}', [\App\Http\Controllers\Api\V5\TimeSlotController::class, 'destroy']);
        });

        Route::prefix('tournaments')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\V5\TournamentController::class, 'getAll']);
            Route::post('/', [\App\Http\Controllers\Api\V5\TournamentController::class, 'create']);
            Route::get('/{id}', [\App\Http\Controllers\Api\V5\TournamentController::class, 'getById']);
            Route::get('/{id}/possible-teams', [\App\Http\Controllers\Api\V5\TournamentController::class, 'getPossibleTeamsForTournament']);
            Route::put('/{id}', [\App\Http\Controllers\Api\V5\TournamentController::class, 'update']);
            Route::delete('/{id}', [\App\Http\Controllers\Api\V5\TournamentController::class, 'destroy']);
        });

        Route::prefix('pools')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\V5\PoolController::class, 'getAll']);
            Route::post('/', [\App\Http\Controllers\Api\V5\PoolController::class, 'createMany']);
            Route::post('/recreate/{tournamentId}', [\App\Http\Controllers\Api\V5\PoolController::class, 'createOrUpdate']);
            Route::get('/{id}', [\App\Http\Controllers\Api\V5\PoolController::class, 'getById']);
            Route::put('/{id}', [\App\Http\Controllers\Api\V5\PoolController::class, 'update']);
            Route::delete('/{id}', [\App\Http\Controllers\Api\V5\PoolController::class, 'destroy']);
        });

        Route::prefix('rounds')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\V5\RoundController::class, 'getAll']);
            Route::post('/', [\App\Http\Controllers\Api\V5\RoundController::class, 'createMany']);
            Route::put('/attach-to-tournament', [\App\Http\Controllers\Api\V5\RoundController::class, 'updateMany']);
            Route::post('/recreate', [\App\Http\Controllers\Api\V5\RoundController::class, 'recreate']);
            Route::delete('/delete-generated', [\App\Http\Controllers\Api\V5\RoundController::class, 'deleteGeneratedRounds']);
            Route::delete('/delete-rounds', [\App\Http\Controllers\Api\V5\RoundController::class, 'deleteGeneratedRoundsByIds']);
            Route::get('/{id}', [\App\Http\Controllers\Api\V5\RoundController::class, 'getById']);
            Route::put('/{id}', [\App\Http\Controllers\Api\V5\RoundController::class, 'update']);
            Route::delete('/{id}', [\App\Http\Controllers\Api\V5\RoundController::class, 'destroy']);
        });

        Route::prefix('leagues')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\V5\LeagueController::class, 'getAll']);
            Route::post('/', [\App\Http\Controllers\Api\V5\LeagueController::class, 'create']);
            Route::get('/{id}', [\App\Http\Controllers\Api\V5\LeagueController::class, 'getById']);
            Route::put('/{id}', [\App\Http\Controllers\Api\V5\LeagueController::class, 'update']);
            Route::delete('/{id}', [\App\Http\Controllers\Api\V5\LeagueController::class, 'destroy']);
        });

        Route::prefix('conflicts')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\V5\ConflictController::class, 'getAll']);
            Route::post('/', [\App\Http\Controllers\Api\V5\ConflictController::class, 'create']);
            Route::get('/{id}', [\App\Http\Controllers\Api\V5\ConflictController::class, 'getById']);
            Route::put('/{id}', [\App\Http\Controllers\Api\V5\ConflictController::class, 'update']);
            Route::delete('/{id}', [\App\Http\Controllers\Api\V5\ConflictController::class, 'destroy']);
        });

        Route::prefix('suggestions')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\V5\SuggestionController::class, 'getAll']);
            Route::post('/', [\App\Http\Controllers\Api\V5\SuggestionController::class, 'create']);
            Route::get('/{id}', [\App\Http\Controllers\Api\V5\SuggestionController::class, 'getById']);
            Route::put('/{id}', [\App\Http\Controllers\Api\V5\SuggestionController::class, 'update']);
            Route::delete('/{id}', [\App\Http\Controllers\Api\V5\SuggestionController::class, 'destroy']);
        });

        Route::prefix('games')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\V5\GamesController::class, 'getGames']);
            Route::get('/counts', [\App\Http\Controllers\Api\V5\GamesController::class, 'getGamesCount']);
            Route::get('/game-refs', [\App\Http\Controllers\Api\V5\GamesController::class, 'getGamesWithRefs']);
            Route::get('/moved', [\App\Http\Controllers\Api\V5\GamesController::class, 'getMovedGames']);
            Route::get('/cancelled', [\App\Http\Controllers\Api\V5\GamesController::class, 'getCancelledGames']);
            Route::post('/', [\App\Http\Controllers\Api\V5\GamesController::class, 'create']);
            Route::post('/tournaments/{tournamentId}', [\App\Http\Controllers\Api\V5\GamesController::class, 'createAllTournamentGames']);
            Route::delete('/tournaments/{tournamentId}', [\App\Http\Controllers\Api\V5\GamesController::class, 'deleteTournamentGames']);
            Route::get('/{id}', [\App\Http\Controllers\Api\V5\GamesController::class, 'getById']);
            Route::get('/{id}/courts', [\App\Http\Controllers\Api\V5\GamesController::class, 'getCourtsForGame']);
            Route::get('/{gameId}/check-conflicts', [\App\Http\Controllers\Api\V5\GamesController::class, 'checkForGameConflict']);
            Route::put('/{id}', [\App\Http\Controllers\Api\V5\GamesController::class, 'update']);
            Route::put('/{id}/home-away', [\App\Http\Controllers\Api\V5\GamesController::class, 'changeHomeAway']);
            Route::put('/{id}/time-court', [\App\Http\Controllers\Api\V5\GamesController::class, 'saveTimeAndCourt']);
            Route::put('/{id}/organizer-club', [\App\Http\Controllers\Api\V5\GamesController::class, 'setOrganizerClub']);
            Route::post('/{id}/check', [\App\Http\Controllers\Api\V5\GamesController::class, 'checkGame']);
            Route::post('/{gameId}/postpone', [\App\Http\Controllers\Api\V5\GamesController::class, 'postponeMatch']);
            Route::delete('/{id}', [\App\Http\Controllers\Api\V5\GamesController::class, 'destroy']);
        });

        Route::prefix('game-plans')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\V5\GamePlanController::class, 'finAll']);
            Route::post('/', [\App\Http\Controllers\Api\V5\GamePlanController::class, 'create']);
            Route::put('/{id}', [\App\Http\Controllers\Api\V5\GamePlanController::class, 'update']);
        });

        Route::prefix('game-penalties')->group(function () {
            Route::post('/', [\App\Http\Controllers\Api\V5\GamePenaltiesController::class, 'updateOrCreate']);
        });

        Route::prefix('game-notes')->group(function () {
            Route::post('/', [\App\Http\Controllers\Api\V5\GameNotesController::class, 'create']);
            Route::delete('/{id}', [\App\Http\Controllers\Api\V5\GameNotesController::class, 'delete']);
        });

        Route::prefix('calendars')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\V5\CalendarController::class, 'getClubs']);
        });

        Route::prefix('blocked-periods')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\V5\BlockedPeriodsController::class, 'getAll']);
            Route::post('/', [\App\Http\Controllers\Api\V5\BlockedPeriodsController::class, 'create']);
            Route::get('/{id}', [\App\Http\Controllers\Api\V5\BlockedPeriodsController::class, 'getById']);
            Route::put('/{id}', [\App\Http\Controllers\Api\V5\BlockedPeriodsController::class, 'update']);
            Route::delete('/{id}', [\App\Http\Controllers\Api\V5\BlockedPeriodsController::class, 'destroy']);
        });

        Route::prefix('user-season-sports')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\V5\UserSeasonSportsController::class, 'getAll']);
            Route::post('/', [\App\Http\Controllers\Api\V5\UserSeasonSportsController::class, 'create']);
            Route::get('/{id}', [\App\Http\Controllers\Api\V5\UserSeasonSportsController::class, 'getById']);
            Route::put('/{id}', [\App\Http\Controllers\Api\V5\UserSeasonSportsController::class, 'update']);
            Route::delete('/{id}', [\App\Http\Controllers\Api\V5\UserSeasonSportsController::class, 'destroy']);
        });

        Route::prefix('season-sport')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\V5\SeasonSportController::class, 'findAll']);
            Route::post('/', [\App\Http\Controllers\Api\V5\SeasonSportController::class, 'create']);
            Route::get('/{id}', [\App\Http\Controllers\Api\V5\SeasonSportController::class, 'getById']);
            Route::put('/{id}', [\App\Http\Controllers\Api\V5\SeasonSportController::class, 'update']);
            Route::delete('/{id}', [\App\Http\Controllers\Api\V5\SeasonSportController::class, 'remove']);
        });

        Route::prefix('regions')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\V5\RegionsController::class, 'getAll']);
            Route::post('/', [\App\Http\Controllers\Api\V5\RegionsController::class, 'create']);
            Route::get('/{id}', [\App\Http\Controllers\Api\V5\RegionsController::class, 'getById']);
            Route::put('/{id}', [\App\Http\Controllers\Api\V5\RegionsController::class, 'update']);
            Route::delete('/{id}', [\App\Http\Controllers\Api\V5\RegionsController::class, 'destroy']);
        });

        Route::prefix('reservation-types')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\V5\ReservationTypesController::class, 'getAll']);
            Route::post('/', [\App\Http\Controllers\Api\V5\ReservationTypesController::class, 'create']);
            Route::get('/{id}', [\App\Http\Controllers\Api\V5\ReservationTypesController::class, 'getById']);
            Route::put('/{id}', [\App\Http\Controllers\Api\V5\ReservationTypesController::class, 'update']);
            Route::delete('/{id}', [\App\Http\Controllers\Api\V5\ReservationTypesController::class, 'destroy']);
        });

        Route::prefix('tournament-types')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\V5\TournamentTypesController::class, 'getAll']);
            Route::post('/', [\App\Http\Controllers\Api\V5\TournamentTypesController::class, 'create']);
            Route::get('/{id}', [\App\Http\Controllers\Api\V5\TournamentTypesController::class, 'getById']);
            Route::put('/{id}', [\App\Http\Controllers\Api\V5\TournamentTypesController::class, 'update']);
            Route::delete('/{id}', [\App\Http\Controllers\Api\V5\TournamentTypesController::class, 'destroy']);
        });

        Route::prefix('tournament-structures')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\V5\TournamentStructuresController::class, 'getAll']);
            Route::post('/', [\App\Http\Controllers\Api\V5\TournamentStructuresController::class, 'create']);
            Route::get('/{id}', [\App\Http\Controllers\Api\V5\TournamentStructuresController::class, 'getById']);
            Route::put('/{id}', [\App\Http\Controllers\Api\V5\TournamentStructuresController::class, 'update']);
            Route::delete('/{id}', [\App\Http\Controllers\Api\V5\TournamentStructuresController::class, 'destroy']);
        });

        Route::prefix('tournament-registration-types')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\V5\TournamentRegistrationTypesController::class, 'getAll']);
            Route::post('/', [\App\Http\Controllers\Api\V5\TournamentRegistrationTypesController::class, 'create']);
            Route::get('/{id}', [\App\Http\Controllers\Api\V5\TournamentRegistrationTypesController::class, 'getById']);
            Route::put('/{id}', [\App\Http\Controllers\Api\V5\TournamentRegistrationTypesController::class, 'update']);
            Route::delete('/{id}', [\App\Http\Controllers\Api\V5\TournamentRegistrationTypesController::class, 'destroy']);
        });

        Route::prefix('tournament-programs')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\V5\TournamentProgramsController::class, 'getAll']);
            Route::post('/', [\App\Http\Controllers\Api\V5\TournamentProgramsController::class, 'create']);
            Route::get('/{id}', [\App\Http\Controllers\Api\V5\TournamentProgramsController::class, 'getById']);
            Route::put('/{id}', [\App\Http\Controllers\Api\V5\TournamentProgramsController::class, 'update']);
            Route::delete('/{id}', [\App\Http\Controllers\Api\V5\TournamentProgramsController::class, 'destroy']);
        });

        Route::prefix('tournament-group')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\V5\TournamentGroupController::class, 'getAll']);
            Route::get('/names', [\App\Http\Controllers\Api\V5\TournamentGroupController::class, 'getNames']);
            Route::post('/', [\App\Http\Controllers\Api\V5\TournamentGroupController::class, 'create']);
            Route::get('/{id}', [\App\Http\Controllers\Api\V5\TournamentGroupController::class, 'getById']);
            Route::get('/{id}/teams', [\App\Http\Controllers\Api\V5\TournamentGroupController::class, 'getTeamsByGroupId']);
            Route::get('/{id}/possible-teams', [\App\Http\Controllers\Api\V5\TournamentGroupController::class, 'getPossibleTeamsForGroup']);
            Route::put('/{id}', [\App\Http\Controllers\Api\V5\TournamentGroupController::class, 'update']);
            Route::delete('/{id}', [\App\Http\Controllers\Api\V5\TournamentGroupController::class, 'destroy']);
        });

        Route::prefix('tournament-configs')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\V5\TournamentConfigsController::class, 'getAll']);
            Route::get('/names', [\App\Http\Controllers\Api\V5\TournamentConfigsController::class, 'getNames']);
            Route::post('/', [\App\Http\Controllers\Api\V5\TournamentConfigsController::class, 'create']);
            Route::get('/{id}', [\App\Http\Controllers\Api\V5\TournamentConfigsController::class, 'getById']);
            Route::put('/{id}', [\App\Http\Controllers\Api\V5\TournamentConfigsController::class, 'update']);
            Route::delete('/{id}', [\App\Http\Controllers\Api\V5\TournamentConfigsController::class, 'destroy']);
        });

        Route::prefix('court-priorities')->group(function () {
            Route::get('/{id}', [\App\Http\Controllers\Api\V5\CourtPriorityController::class, 'getById']);
            Route::post('/', [\App\Http\Controllers\Api\V5\CourtPriorityController::class, 'create']);
            Route::post('/bulk', [\App\Http\Controllers\Api\V5\CourtPriorityController::class, 'createBulk']);
            Route::delete('/{id}', [\App\Http\Controllers\Api\V5\CourtPriorityController::class, 'destroy']);
        });

        Route::prefix('registration')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\V5\RegistrationController::class, 'getRegistrations']);
            Route::post('/', [\App\Http\Controllers\Api\V5\RegistrationController::class, 'create']);
            Route::post('/bulk', [\App\Http\Controllers\Api\V5\RegistrationController::class, 'saveBulkRegistration']);
            Route::get('/{id}', [\App\Http\Controllers\Api\V5\RegistrationController::class, 'getById']);
            Route::put('/{id}', [\App\Http\Controllers\Api\V5\RegistrationController::class, 'update']);
            Route::delete('/{id}', [\App\Http\Controllers\Api\V5\RegistrationController::class, 'destroy']);
        });

        Route::prefix('coach-education')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\V5\CoachEducationController::class, 'getAllCoachEducations']);
            Route::post('/', [\App\Http\Controllers\Api\V5\CoachEducationController::class, 'createEducation']);
            Route::get('/{id}', [\App\Http\Controllers\Api\V5\CoachEducationController::class, 'getById']);
            Route::delete('/{id}', [\App\Http\Controllers\Api\V5\CoachEducationController::class, 'destroy']);
        });

        Route::prefix('coach-history')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\V5\CoachHistoryController::class, 'getAllCoachHistories']);
            Route::post('/', [\App\Http\Controllers\Api\V5\CoachHistoryController::class, 'create']);
            Route::get('/{id}', [\App\Http\Controllers\Api\V5\CoachHistoryController::class, 'getCoachHistoryById']);
        });

        Route::prefix('coach-licenses')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\V5\CoachLicenseController::class, 'getAllCoachLicenses']);
            Route::post('/', [\App\Http\Controllers\Api\V5\CoachLicenseController::class, 'create']);
        });

        Route::prefix('game-draft')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\V5\GameDraftController::class, 'index']);
        });
    });
});
