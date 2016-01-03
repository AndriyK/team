<?php 
$I = new ApiTester($scenario);
$I->wantTo('check games resource');

$I->sendGET('/teams/26?expand=games');
$I->seeResponseCodeIs(401);

$token = $I->login('q@q.q', 'q');

// show list of games for team
$I->haveHttpHeader('Authorization', "Bearer $token");
$I->sendGET('/teams/26?expand=games');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('games');
$games = $I->grabDataFromResponseByJsonPath('$.games')[0];
\PHPUnit_Framework_Assert::assertEquals(2, count($games));
$I->seeResponseContains('training');
$I->seeResponseContains('evening game');

// check for empty list for team with no games
$I->sendGET('/teams/27?expand=games');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('games');
$games = $I->grabDataFromResponseByJsonPath('$.games')[0];
\PHPUnit_Framework_Assert::assertEquals(0, count($games));

// create game
$I->sendPOST('/games', ['team_id'=> 26, 'datetime' => "2016-01-05 10:00:00", 'location' => "home", 'title' => "important game"]);
$I->seeResponseCodeIs(201);
$I->seeResponseIsJson();
$I->seeResponseContains('important game');
$I->seeResponseContains('home');
$gameId = $I->grabDataFromResponseByJsonPath('$.id')[0];
$I->sendGET('/teams/26?expand=games');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('games');
$games = $I->grabDataFromResponseByJsonPath('$.games')[0];
\PHPUnit_Framework_Assert::assertEquals(3, count($games));

// Join one player to the game
$I->sendPUT("/games/{$gameId}", ['join_player' => 33]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();

// Remove player from the game
$I->sendPUT("/games/{$gameId}", ['reject_player' => 33]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();

// delete game
$I->sendDELETE("/games/{$gameId}");
$I->seeResponseCodeIs(204);
$I->sendGET('/teams/26?expand=games');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('games');
$games = $I->grabDataFromResponseByJsonPath('$.games')[0];
\PHPUnit_Framework_Assert::assertEquals(2, count($games));