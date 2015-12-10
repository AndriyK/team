<?php
namespace app\api\modules\v1\controllers;
 
use yii\rest\ActiveController;
 
class PlayerController extends ActiveController
{
    public $modelClass = 'app\models\Player';
}