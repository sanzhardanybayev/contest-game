<?php

use Illuminate\Http\Request;
use App\Team;
use App\Group;
use App\FirstTour;
use App\PlayOff;
use App\Quarter;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

header("Access-Control-Allow-Origin: *");
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/teams', function (Request $request) {
    return response()->json(['teams' => Team::orderBy('name')->get()]);
});

Route::get('/create/teams', function (Request $request) {

    $teams = Team::all();
    if (sizeof($teams) < 1) {
        for ($i = 0; $i < 16; $i++) {
            $team = new Team;
            $team->name = 'Team-' . ($i + 1);

            $team->save();
        }

        return response()
            ->json(['status' => '200', 'message' => 'Successfully created 16 teams!']);
    } else {
        return response()
            ->json(['status' => '500', 'message' => 'Teams were created!']);
    }
});


Route::get('/divide', function () {

    $teams = Team::all();

    if (sizeof($teams) > 0) {
        $groups = Group::all();

        if (sizeof($groups) > 0) {
            $agroups = Group::where('group', 'A')->with('team')->get();
            $bgroups = Group::where('group', 'B')->with('team')->get();
            return response()->json(['agroups' => $agroups, 'bgroups' => $bgroups]);
          
        } else {
            $groups = array_fill(0, 16, 0);
            $min = 0;
            $max = 15;


            while (array_sum($groups) < 8) {
                $teamNumber = rand($min, $max);
                $teamNumber = $teamNumber > 0 ? $teamNumber - 1 : $teamNumber;
                if ($groups[$teamNumber] == 1) {
                    continue;
                } else {
                    $groups[$teamNumber] = 1;
                }
            }

            for ($i = 0; $i < 16; $i++) {
                $group = new Group;
                $groupName = '';
                switch ($groups[$i]) {
                    case 1:
                        $groupName = 'B';
                        break;
                    default:
                        $groupName = 'A';
                        break;
                }

                $group->team_id = $teams[$i]->id;
                $group->group = $groupName;

                $group->score_matches = 0;
                $group->score_playoff = 0;
                $group->score_quarter = 0;
                $group->winner = false;

                $group->save();
            }


            $agroups = Group::where('group', 'A')->with('team')->get();
            $bgroups = Group::where('group', 'B')->with('team')->get();
            return response()->json(['agroups' => $agroups, 'bgroups' => $bgroups]);


        }


    } else {
        return response()
            ->json(['status' => '500', 'message' => 'You should create teams first!']);
    }
});

Route::get('/first_play', function () {

    $teams = Team::all();
    $firstTour = FirstTour::all();

    if (sizeof($teams) > 0 && sizeof($firstTour) < 1) {
        $firstGroup = Group::where('group', 'A')->get();
        $secondGroup = Group::where('group', 'B')->get();


        if (sizeof($firstGroup) > 0 && sizeof($secondGroup) > 0) {

            for ($i = 0; $i < sizeof($firstGroup); $i++) {
                $team = Group::where('team_id', $firstGroup[$i]->team_id)->first();


                for ($j = 0; $j < sizeof($firstGroup); $j++) {
                    if ($j == $i) {
                        continue;
                    } else {
                        $secondTeam = Group::where('team_id', $firstGroup[$j]->team_id)->first();
                        $firstTeamScore = rand(0, 10);
                        $secondTeamScore = rand(0, 10);

                        while ($firstTeamScore == $secondTeamScore) {
                            $secondTeamScore = rand(0, 10);
                        }

                        $game = new FirstTour;
                        $game->first_player_id = $firstGroup[$i]->team_id;
                        $game->second_player_id = $firstGroup[$j]->team_id;
                        $game->first_player_score = $firstTeamScore;
                        $game->second_player_score = $secondTeamScore;

                        $team->score_matches = $team->score_matches + $firstTeamScore;
                        $secondTeam->score_matches = $secondTeam->score_matches + $secondTeamScore;

                        $team->save();
                        $secondTeam->save();

                        if ($firstTeamScore > $secondTeamScore) {
                            $game->winner_id = $firstGroup[$i]->team_id;
                        } else {
                            $game->winner_id = $firstGroup[$j]->team_id;
                        }

                        $game->save();

                    }
                }

            }

            for ($i = 0; $i < sizeof($secondGroup); $i++) {
                $team = Group::where('team_id', $secondGroup[$i]->team_id)->first();
                for ($j = 0; $j < sizeof($secondGroup); $j++) {
                    if ($j == $i) {
                        continue;
                    } else {
                        $secondTeam = Group::where('team_id', $secondGroup[$j]->team_id)->first();
                        $firstTeamScore = rand(0, 10);
                        $secondTeamScore = rand(0, 10);

                        while ($firstTeamScore == $secondTeamScore) {
                            $secondTeamScore = rand(0, 10);
                        }

                        $game = new FirstTour;
                        $game->first_player_id = $secondGroup[$i]->team_id;
                        $game->second_player_id = $secondGroup[$j]->team_id;
                        $game->first_player_score = $firstTeamScore;
                        $game->second_player_score = $secondTeamScore;

                        $team->score_matches = $team->score_matches + $firstTeamScore;
                        $secondTeam->score_matches = $secondTeam->score_matches + $secondTeamScore;

                        $team->save();
                        $secondTeam->save();

                        if ($firstTeamScore > $secondTeamScore) {
                            $game->winner_id = $secondGroup[$i]->team_id;
                        } else {
                            $game->winner_id = $secondGroup[$j]->team_id;
                        }

                        $game->save();

                    }
                }

            }

            return response()
                ->json(['status' => '200', 'message' => 'First tour has just completed!!']);


        } else {
            return response()
                ->json(['status' => '500', 'message' => 'You should create groups first!']);
        }
    } else {
        return response()
            ->json(['status' => '500', 'message' => 'You should create teams first!']);
    }
});


Route::get('/groups', function ()  {

});

Route::get('/playoff', function () {

    $teams = Team::all();
    $firstTour = FirstTour::all();
    $groups = Group::all();
    $playoff = PlayOff::all();

    if (sizeof($teams) > 0 && sizeof($firstTour) > 0 && sizeof($groups) > 0 && sizeof($playoff) < 1) {

        $firstGroup = Group::where('group', 'A')->orderBy('score_matches', 'desc')->take(4)->get();
        $secondGroup = Group::where('group', 'B')->orderBy('score_matches', 'desc')->take(4)->get();


        $min = 0;
        $max = 10;

        $worstTeamIndex = sizeof($firstGroup) - 1;

        for ($i = 0; $i < sizeof($firstGroup); $i++) {

            $playoff = new PlayOff;

            $firstTeamScore = rand($min, $max);
            $secondTeamScore = rand($min, $max);

            while ($firstTeamScore == $secondTeamScore) {
                $secondTeamScore = rand($min, $max);
            }

            $firstGroup[$i]->score_playoff = $firstTeamScore;
            $firstGroup[$i]->save();
            $secondGroup[$worstTeamIndex]->score_playoff = $secondTeamScore;
            $secondGroup[$worstTeamIndex]->save();

            $playoff->first_player_id = $firstGroup[$i]->team_id;
            $playoff->second_player_id= $secondGroup[$worstTeamIndex]->team_id;

            if($firstTeamScore > $secondTeamScore){
                $playoff->winner_id = $firstGroup[$i]->team_id;
            } else {
                $playoff->winner_id = $secondGroup[$worstTeamIndex]->team_id;
            }

            $playoff->save();

            $worstTeamIndex--;

        }

        return response()
            ->json(['status' => '200', 'message' => 'Playoff Successfully completed']);

    } else {
        return response()
            ->json(['status' => '500', 'message' => 'You should create teams first , divide them by groups and  go through the first tour!']);
    }

});

Route::get('/finalgame', function(){


    $teams = Team::all();
    $firstTour = FirstTour::all();
    $groups = Group::all();
    $playoff = PlayOff::all();
    $quarter = Quarter::all();


    if (sizeof($teams) > 0 && sizeof($firstTour) > 0 && sizeof($groups) > 0 && sizeof($playoff) > 1 && sizeof($quarter) < 1) {

        $firstGroup = Group::where('group', 'A')->orderBy('score_playoff', 'desc')->first();
        $secondGroup = Group::where('group', 'B')->orderBy('score_playoff', 'desc')->first();

        $min = 0;
        $max = 10;

        $firstTeamScore = rand($min, $max);
        $secondTeamScore = rand($min, $max);

        while ($firstTeamScore == $secondTeamScore) {
            $secondTeamScore = rand($min, $max);
        }

        $firstGroup->score_quarter = $firstTeamScore;
        $secondGroup->score_quarter = $secondTeamScore;

        if($firstTeamScore > $secondTeamScore){
            $firstGroup->winner = 1;
        } else {
            $firstGroup->winner = 1;
        }

        $teamWinner = Team::find($firstGroup->team_id);

        return response()
            ->json(['status' => '200', 'message' => 'The winner is ' . $teamWinner->name]);


    } else {
        return response()
            ->json(['status' => '500', 'message' => 'You should create teams first , divide them by groups , go through the first tour and go through playoff!']);
    }
});

