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
    public $is_capitan;


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
        return ['id', 'email', 'name', 'is_capitan'];
    }

    public function extraFields()
    {
        return ['teams'];
    }

    public function getTeams()
    {
        return $this->hasMany(Team::className(), ['id' => 'team_id'])
            ->viaTable('team_has_player', ['player_id' => 'id'])
            ->select('*, (SELECT is_capitan FROM team_has_player WHERE player_id='.$this->id.' AND team_id=teams.id LIMIT 1) as is_capitan');
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

    public static function findByMail($email)
    {
        return static::findOne(['email' => $email]);
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
        $this->password = Yii::$app->security->generatePasswordHash($this->password);
        $this->token = Yii::$app->security->generateRandomString();
        return parent::beforeSave($insert);
    }

    /**
     * Validates password
     *
     * @param  string  $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }

    public function toArray(array $fields = [], array $expand = [], $recursive = true)
    {
        return array_filter(parent::toArray($fields,$expand,$recursive), function($val){
            return is_null($val) ? false : true;
        });
    }
}