<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "players".
 *
 * @property integer $id
 * @property string $email
 * @property string $password
 * @property string $password_repeat
 * @property string $token
 * @property string $name
 * @property string $created_at
 */
class Player extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
    public $password_repeat;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'players';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email', 'password', 'password_repeat', 'name'], 'required'],
            [['email', 'password', 'name'], 'string', 'max' => 50],
            ['password', 'compare'],
            [['email'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'email' => 'Email',
            'password' => 'Password',
            'name' => 'Name',
        ];
    }

    /**
     * @inheritdoc
     */
    public function fields()
    {
        return ['id', 'email', 'name', 'token'];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['token' => $token]);
    }

    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return '';
    }

    public function validateAuthKey($authKey)
    {
        return '';
    }

    public function beforeSave($insert) {
        if(isset($this->password)){
            $this->password = Yii::$app->getSecurity()->generatePasswordHash($this->password);
        }
        $this->token = Yii::$app->security->generateRandomString();

        return parent::beforeSave($insert);
    }
}