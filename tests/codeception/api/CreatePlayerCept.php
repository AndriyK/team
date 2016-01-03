<?php
$user_data = ['name' => 'Player 1', 'email' => 'test@mail.com', 'password' => 'test', 'password_repeat' => 'test'];

$I = new ApiTester($scenario);
$I->wantTo('check that new player can be created and player with already existed mail not');
$I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
$I->sendPOST('/players', $user_data);
$I->seeResponseCodeIs(201);
$I->seeResponseIsJson();
$I->seeResponseContains('token');

$user_data['email'] = 'q@q.q';
$I->sendPOST('/players', $user_data);
$I->seeResponseCodeIs(422);
$I->seeResponseIsJson();
$I->seeResponseContains('Email \"q@q.q\" has already been taken.');

