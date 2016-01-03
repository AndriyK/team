<?php
$I = new ApiTester($scenario);
$I->wantTo('check teams search by name and player mail');

$I->sendGET('/teams/search?name=test&email=');
$I->seeResponseCodeIs(401);

$token = $I->login('q@q.q', 'q');

// search by team name
$I->haveHttpHeader('Authorization', "Bearer $token");
$I->sendGET('/teams/search?name=play&email=');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('"name":"FridayPlay"');
$I->seeResponseContains('"name":"MondayPlay"');
$teams = $I->grabDataFromResponseByJsonPath('$')[0];
\PHPUnit_Framework_Assert::assertEquals(2, count($teams));

// search by email
$I->sendGET('/teams/search?name=&email=w@w.w');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('"name":"FridayPlay"');
$teams = $I->grabDataFromResponseByJsonPath('$')[0];
\PHPUnit_Framework_Assert::assertEquals(1, count($teams));

// no team parameters
$I->sendGET('/teams/search?name=&email=wrong@mail.com');
$I->seeResponseCodeIs(404);
$I->seeResponseIsJson();
$I->seeResponseContains('"No teams found for passed name and mail"');

// bad request with empty parameters
$I->sendGET('/teams/search?name=&email=');
$I->seeResponseCodeIs(400);
$I->seeResponseIsJson();
$I->seeResponseContains('"There are wrong or empty query parameters"');