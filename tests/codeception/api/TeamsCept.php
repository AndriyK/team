<?php 
$I = new ApiTester($scenario);
$I->wantTo('check teams resource');

$I->sendGET('/players/33?expand=teams');
$I->seeResponseCodeIs(401);

$token = $I->login('q@q.q', 'q');

// show list of user's teams
$I->haveHttpHeader('Authorization', "Bearer $token");
$I->sendGET('/players/33?expand=teams');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('teams');
$I->seeResponseContains('"sport":"football"');
$I->seeResponseContains('"name":"FridayPlay"');
$I->seeResponseContains('"is_capitan":"1"');
$teams = $I->grabDataFromResponseByJsonPath('$.teams')[0];
\PHPUnit_Framework_Assert::assertEquals(1, count($teams));
\PHPUnit_Framework_Assert::assertEquals(2, count($teams[0]['players']));

// new team creation
$I->sendPOST('/teams', ['name' => "QA", 'sport' => "voleyball"]);
$I->seeResponseCodeIs(201);
$I->seeResponseIsJson();
$I->seeResponseContains('"sport":"voleyball"');
$I->seeResponseContains('"name":"QA"');
$I->seeResponseContains('"is_capitan":"1"');
$players = $I->grabDataFromResponseByJsonPath('$.players')[0];
\PHPUnit_Framework_Assert::assertEquals(1, count($players));
$teamId = $I->grabDataFromResponseByJsonPath('$.id')[0];
$I->sendGET('/players/33?expand=teams');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('teams');
$teams = $I->grabDataFromResponseByJsonPath('$.teams')[0];
\PHPUnit_Framework_Assert::assertEquals(2, count($teams));

// Join one player to the team
$I->sendPUT("/teams/{$teamId}", ['join_player' => 'w@w.w']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$players = $I->grabDataFromResponseByJsonPath('$.players')[0];
\PHPUnit_Framework_Assert::assertEquals(2, count($players));

// Remove player from the team
$I->sendPUT("/teams/{$teamId}", ['remove_player' => 'w@w.w']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$players = $I->grabDataFromResponseByJsonPath('$.players')[0];
\PHPUnit_Framework_Assert::assertEquals(1, count($players));

// update team name
$I->sendPUT("/teams/{$teamId}", ['name' => 'QA2']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('"name":"QA2"');

// delete team
$I->sendDELETE("/teams/{$teamId}");
$I->seeResponseCodeIs(204);
$I->sendGET('/players/33?expand=teams');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('teams');
$teams = $I->grabDataFromResponseByJsonPath('$.teams[0]');
\PHPUnit_Framework_Assert::assertEquals(1, count($teams));


// check that all other routes are disabled
// index
$I->sendGET('/teams');
$I->seeResponseCodeIs(405);



