<?php
namespace app\api\modules\v1\controllers;
 
use yii\rest\ActiveController;
use yii\filters\ContentNegotiator;
use yii\web\Response;
use yii\filters\auth\HttpBearerAuth;
 
class PlayerController extends ActiveController
{
    public $modelClass = 'app\models\Player';

    public $createScenario = \app\models\Player::CREATE_SCENARIO;

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::className(),
            'only' => ['view', 'index'],
        ];

        $behaviors['contentNegotiator'] = [
            'class' => ContentNegotiator::className(),
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ],
        ];

        /*$behaviors['access'] = [
            'class' => AccessControl::className(),
            'only' => ['dashboard'],
            'rules' => [
                [
                    'actions' => ['dashboard'],
                    'allow' => true,
                    'roles' => ['@'],
                ],
            ],
        ];*/

        return $behaviors;
    }

}