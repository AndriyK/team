<?php
$I = new ApiTester($scenario);
$I->wantTo('check player dashboard');

$I->sendGET('/dashboard/33');
$I->seeResponseCodeIs(401);

$token = $I->login('q@q.q', 'q');
$I->haveHttpHeader('Authorization', "Bearer $token");

$I->sendGET('/dashboard/33');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$games = $I->grabDataFromResponseByJsonPath('$')[0];
\PHPUnit_Framework_Assert::assertEquals(2, count($games));

$game1 = $games[0];
\PHPUnit_Framework_Assert::assertEquals('joined', $game1['current_player_status']);
\PHPUnit_Framework_Assert::assertEquals('FridayPlay', $game1['team']['name']);
\PHPUnit_Framework_Assert::assertEquals('training', $game1['game']['title']);
\PHPUnit_Framework_Assert::assertEquals(2, $game1['players_summary']['total']['amount']);
\PHPUnit_Framework_Assert::assertEquals(1, $game1['players_summary']['joined']['amount']);
\PHPUnit_Framework_Assert::assertEquals('q', $game1['players_summary']['joined']['players'][0]);
\PHPUnit_Framework_Assert::assertEquals(0, $game1['players_summary']['rejected']['amount']);
\PHPUnit_Framework_Assert::assertEquals(1, $game1['players_summary']['unknown']['amount']);
\PHPUnit_Framework_Assert::assertEquals('Super Player', $game1['players_summary']['unknown']['players'][0]);

$game2 = $games[1];
\PHPUnit_Framework_Assert::assertEquals('rejected', $game2['current_player_status']);
\PHPUnit_Framework_Assert::assertEquals('FridayPlay', $game2['team']['name']);
\PHPUnit_Framework_Assert::assertEquals('evening game', $game2['game']['title']);
\PHPUnit_Framework_Assert::assertEquals(2, $game2['players_summary']['total']['amount']);
\PHPUnit_Framework_Assert::assertEquals(0, $game2['players_summary']['joined']['amount']);
\PHPUnit_Framework_Assert::assertEquals('q', $game2['players_summary']['rejected']['players'][0]);
\PHPUnit_Framework_Assert::assertEquals(1, $game2['players_summary']['rejected']['amount']);
\PHPUnit_Framework_Assert::assertEquals(1, $game2['players_summary']['unknown']['amount']);
\PHPUnit_Framework_Assert::assertEquals('Super Player', $game2['players_summary']['unknown']['players'][0]);


// request with another player
$I->sendGET('/dashboard/333');
$I->seeResponseCodeIs(400);


