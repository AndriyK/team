<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "players".
 *
 * @property integer $id
 * @property string $email
 * @property string $password
 * @property string $token
 * @property string $name
 * @property string $auth_key
 */
class Player extends \yii\db\ActiveRecord
{
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
            [['email', 'password', 'name'], 'required'],
            [['email', 'password', 'name'], 'string', 'max' => 50],
            [['token', 'auth_key'], 'string', 'max' => 100],
            [['email'], 'unique'],
            [['token'], 'unique'],
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
            'token' => 'Token',
            'name' => 'Name',
            'auth_key' => 'Auth Key',
        ];
    }

    /**
     * @inheritdoc
     */
    public function fields()
    {
        return ['id', 'email', 'name'];
    }
}