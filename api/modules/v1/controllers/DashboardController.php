<?php
namespace app\api\modules\v1\controllers;

use app\models\Dashboard;
use yii;
use yii\web\Controller;
use yii\filters\ContentNegotiator;
use yii\web\Response;
use yii\filters\auth\HttpBearerAuth;

class DashboardController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::className(),
        ];

        $behaviors['contentNegotiator'] = [
            'class' => ContentNegotiator::className(),
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ],
        ];

        return $behaviors;
    }

    /**
     * Return stat summary for all scheduled games for passed player
     * @param Integer $player_id
     * @return array
     * @throws \Exception
     */
    public function actionIndex($player_id)
    {
        $player = Yii::$app->user->getIdentity(false);
        if($player_id != $player->id){
            throw new \yii\web\HttpException(400);
        }

        $dashboard = new Dashboard();
        return $dashboard->getPlayerGamesDashboardData();
    }
}