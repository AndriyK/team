<?php
namespace app\api\modules\v1\controllers;

use yii;
use yii\rest\ActiveController;
use yii\filters\ContentNegotiator;
use yii\web\Response;
use yii\filters\auth\HttpBearerAuth;

class TeamController extends ActiveController
{
    public $modelClass = 'app\models\Team';

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

    public function actionSearch()
    {
        $team_name = Yii::$app->request->get('name');
        $player_mail = Yii::$app->request->get('email');

        if(empty($team_name) && empty($player_mail)){
            throw new \yii\web\HttpException(400, 'There are wrong or empty query parameters');
        }

        $teams = $this->performSearch($team_name, $player_mail);
        if(empty($teams)){
            throw new \yii\web\HttpException(404, 'No teams found for passed name and mail');
        }

        return $teams;
    }

    private function performSearch($team_name, $player_mail)
    {
        $res = array_merge(
                $this->performNameSearch($team_name),
                $this->performPlayerMailSearch($player_mail)
            );
        // remove dublicates with same key(id)
        $res = yii\helpers\ArrayHelper::index($res, 'id');
        return array_values($res);
    }

    private function performNameSearch($team_name)
    {
        return \app\models\Team::find()
                ->where(['like', 'name', $team_name])
                ->all();
    }

    private function performPlayerMailSearch($player_mail)
    {
        if( $player = \app\models\Player::findByMail($player_mail) ){
            return $player->teams;
        }

        return array();
    }
}