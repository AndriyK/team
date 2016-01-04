<?php
$user_data = ['name' => 'Player 1', 'email' => 'test@mail.com', 'password' => 'test', 'password_repeat' => 'test'];

$I = new ApiTester($scenario);
$I->wantTo('check players resource');

// creating new player
$I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
$I->sendPOST('/players', $user_data);
$I->seeResponseCodeIs(201);
$I->seeResponseIsJson();
$I->seeResponseContains('token');

// already existing player
$user_data['email'] = 'q@q.q';
$I->sendPOST('/players', $user_data);
$I->seeResponseCodeIs(422);
$I->seeResponseIsJson();
$I->seeResponseContains('Email \"q@q.q\" has already been taken.');

// check that all other routes are disabled
// index
$I->sendGET('/players');
$I->seeResponseCodeIs(404);
$I->seeResponseContains('Page not found.');
// update
$I->sendPUT('/players/33', ['name'=>'updated_name']);
$I->seeResponseCodeIs(404);
$I->seeResponseContains('Page not found.');
// update
$I->sendDELETE('/players/33');
$I->seeResponseCodeIs(404);
$I->seeResponseContains('Page not found.');

