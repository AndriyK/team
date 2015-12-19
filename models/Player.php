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
class Player extends AppActiveRecord implements \yii\web\IdentityInterface
{
    /**
     * Holds passed password_repeat value
     * @var String
     */
    public $password_repeat;

    /**
     * For correct view of many-to-many relation with team model
     * maps "team_has_player.is_capitan" DB value
     * @var int
     */
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
    public function fields()
    {
        return ['id', 'email', 'name', 'is_capitan'];
    }

    /**
     * @inheritdoc
     */
    public function extraFields()
    {
        return ['teams'];
    }

    /**
     * Defines many-to-many relation with team model via table team_has_player
     * @return list of teams to which belongs player
     */
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

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * Search player entry for passed mail
     * @param $email
     * @return null|static
     */
    public static function findByMail($email)
    {
        if(!$email){
            return null;
        }

        return static::findOne(['email' => $email]);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return '';
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return '';
    }

    /**
     * Generates password hash and security tokens
     * @param bool $insert
     * @return bool
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function beforeSave($insert) {
        $this->password = Yii::$app->security->generatePasswordHash($this->password);
        $this->token = Yii::$app->security->generateRandomString();
        return parent::beforeSave($insert);
    }

    /**
     * Validates password
     * @param  string  $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }
}