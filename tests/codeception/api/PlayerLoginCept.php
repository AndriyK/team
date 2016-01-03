<?php 
$I = new ApiTester($scenario);
$I->wantTo('check player login');
$I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
$I->sendPOST('/auth/login', ['email'=>'q@q.q', 'password'=>'q']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('token');

$I->sendPOST('/auth/login', ['email'=>'q@q.q', 'password'=>'wrong_password']);
$I->seeResponseCodeIs(401);
$I->seeResponseIsJson();
$I->seeResponseContains('Incorrect username or password.');
