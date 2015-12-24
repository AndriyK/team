<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * Handle logic for selection games related data and prepare it in clean structure
 * Class Dashboard
 * @package app\models
 */
class Dashboard extends Model
{
    /**
     * Builds array that contain all data regarding playes's games
     * @return array
     */
    public function getPlayerGamesDashboardData()
    {
        $player = Yii::$app->user->getIdentity(false);

        $playerTeams = $this->getPlayerTeams($player);
        $playerGames = $this->getPlayerGames($playerTeams);

        return $this->buildSummaryPerGame($playerTeams, $playerGames);
    }

    /**
     * Returns all teams to which belongs the player
     * @param Player $playerModel
     * @return array
     */
    private function getPlayerTeams($playerModel)
    {
        return ArrayHelper::index($playerModel->teams, 'id');
    }

    /**
     * Build a list of scheduled games for all passed teams
     * @param Array $teams
     * @return array
     */
    private function getPlayerGames($teams)
    {
        $games = [];
        foreach($teams as $team_id => $team){
            foreach($team->games as $game){
                $games[$game->id] = $game;
            }

        }
        return $games;
    }

    /**
     * Build expected statistic structure for all games
     * @param Array $teams
     * @param Array $games
     * @return array
     */
    private function buildSummaryPerGame($teams, $games)
    {
        $res = [];
        foreach($games as $game){
            $gameStat = $this->getGameStatistic($game, $teams);
            $res[] = $gameStat;
        }
        return $res;
    }

    /**
     * Buils stat structure for single game.
     * Expected structure is:
     {
        date: "2015-12-22",
        current_player_status: "joined",
        team: {
            "id": 20,
            "name": 'team name',
            "sport": 'footbal'
        },
        game: {
            "id": 45,
            "title": 'training',
            "datetime": '2015-12-22 12:00:00',
            "location": 'field 23'
        },
        players_summary: {
            "total": {
                "amount":14                     // total amount players in the team (team_has_player.team_id.*)
            },
            "joined": {
                "amount": 2,                    // players that confirmed presence (games_has_player.presence.1)
                "players": ['name1', 'name2']
            },
            "rejected": {
                "amount": 5,                    // games_has_player.presence.0
                "players": ['name4', 'name3']
            },
            "unknown": {
                "amount": 7,                    // total - joined - rejected
                "players": ['name5', 'name6']
            }
        }
    }
     * @param Game $game
     * @param $teams
     * @return array
     */
    private function getGameStatistic($game, $teams)
    {
        $gameTeam = $teams[$game->team_id];
        $gamePlayers = $game->players;

        $stat = [];
        $stat['date'] = $game->datetime;
        $stat['current_player_status'] = $this->getCurrentPlayerPresenceStatus($gamePlayers);
        $stat['team'] = [
            'id' => $gameTeam->id,
            'name' => $gameTeam->name,
            'sport' => $gameTeam->sport
        ];
        $stat['game'] = $game;

        $playersSummary = [];
        $playersSummary['total'] = ['amount' => count($gameTeam->players)];
        $playersSummary['joined'] = $this->getPlayersStatPerPresenceStatus($gamePlayers, 1);
        $playersSummary['rejected'] = $this->getPlayersStatPerPresenceStatus($gamePlayers, 0);

        $unknownAmount = $playersSummary['total']['amount'] - $playersSummary['joined']['amount'] - $playersSummary['rejected']['amount'];
        $unknownPlayers = $this->getPlayersWithoutPresence($gameTeam->players, array_merge($playersSummary['joined']['players'], $playersSummary['rejected']['players']));
        $playersSummary['unknown'] = ['amount' => $unknownAmount, 'players' => $unknownPlayers];

        $stat['players_summary'] = $playersSummary;

        return $stat;
    }

    /**
     * Return presence status of current player
     * @param Array $gamePlayers - list of players that reported their presence for some game
     * @return string (joined | rejected | unknown)
     */
    private function getCurrentPlayerPresenceStatus($gamePlayers)
    {
        $player = Yii::$app->user->getIdentity(false);

        foreach($gamePlayers as $gplayer){
            if($gplayer->id == $player->id){
                return $gplayer->presence == 1 ? 'joined' : 'rejected';
            }
        }

        return 'unknown';
    }

    /**
     * Returns amount and players list which will be present or absent on some game
     * @param Array $players - list of players that reported their presence
     * @param integer $status - 0-rejected, 1- present
     * @return array ['amount' => X, 'players' => ['name1', 'name2']]
     */
    private function getPlayersStatPerPresenceStatus($players, $status)
    {
        $amount = 0;
        $list = [];

        foreach($players as $player){
            if($player->presence != $status){
                continue;
            }
            $amount++;
            $list[] = $player->name;
        }

        return ['amount'=>$amount, 'players'=> $list];
    }

    /**
     * Filters teams players list and return the ones without reported presence status
     * @param Array $all_players - team's players list
     * @param Array $game_players - list of players' names that reported their presence
     * @return array
     */
    private function getPlayersWithoutPresence($all_players, $game_players)
    {
        $list = [];
        foreach($all_players as $player){
            if(in_array($player->name, $game_players)){
                continue;
            }
            $list[] = $player->name;
        }
        return $list;
    }
}