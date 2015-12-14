<?php
namespace app\api\modules\v1\controllers;

use yii;
use app\models\LoginForm;
use yii\web\Controller;
use yii\filters\ContentNegotiator;
use yii\web\Response;

class AuthController extends Controller
{
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator'] = [
            'class' => ContentNegotiator::className(),
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ],
        ];

        return $behaviors;
    }

    public function actionLogin()
    {
        $model = new LoginForm();
        $data = Yii::$app->request->post();
        $model->load($data,'');

        if ($model->validate()) {
            $user = $model->getUser();
            return $user->token;
        } else {
            Yii::$app->response->statusCode = 401;
            return ['errors' => $this->filterErrors($model->getErrors())];
        }
    }

    private function filterErrors($err)
    {
        $res = [];
        foreach($err as $error) {
            $res[] = array_values($error)[0];
        }
        return $res;
    }
}