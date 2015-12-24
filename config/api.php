<?php

$db     = require(__DIR__ . '/db.php');
$params = require(__DIR__ . '/params.php');
 
$config = [
    'id' => 'api',
    'name' => 'TeamApi',
    // Need to get one level up:
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'components' => [
        'request' => [
            'enableCookieValidation' => false,
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    'logFile' => '@app/runtime/logs/api.log',
                ],
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => true,
            'rules' => [
                // /players
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['v1/player'],
                    'except' => ['delete', 'update']
                ],

                // /teams
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['v1/team'],
                    'extraPatterns' => ['GET search' => 'search']
                ],

                // /games
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['v1/game']
                ],

                // /auth/login
                'POST v1/auth/login' => 'v1/auth/login',

                // /dashboard/:player_id
                "GET v1/dashboard/<player_id:\d+>" => "v1/dashboard/index",
            ],
        ], 
        'user' => [
            'identityClass' => 'app\models\Player',
            'enableSession' => false,
            'loginUrl' => null,
        ],
        'db' => $db,
    ],
    'modules' => [
        'v1' => [
            'class' => 'app\api\modules\v1\Module',
        ],
    ],
    'params' => $params,
];
 
return $config;