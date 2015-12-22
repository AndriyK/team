<?php

namespace app\models;

use Yii;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;


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
    const CREATE_SCENARIO = 'create_player';

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
     * Helper attribute for correct view of many-to-many relation with player model
     * (shows which player reported his presence on the game)
     * @var int
     */
    public $presence;

    /**
     * Flag that indicated when new record was inserted
     * (is populated in beforeSave method)
     * @var bool
     */
    private $isNewRecord = false;

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
            [['password_repeat'], 'required', 'on' => self::CREATE_SCENARIO],
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
        if($this->scenario == self::CREATE_SCENARIO){
            return ['token'];
        }

        return ['id', 'email', 'name', 'is_capitan', 'presence'];
    }

    /**
     * @inheritdoc
     */
    public function extraFields()
    {
        return ['teams', 'games'];
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
     * Defines many-to-many relation with game model via table game_has_player
     * @return list of games where user has marked his presence
     */
    public function getGames()
    {
        return $this->hasMany(Game::className(), ['id' => 'game_id'])
            ->viaTable('game_has_player', ['player_id' => 'id'])
            ->select('*, (SELECT presence FROM game_has_player WHERE player_id='.$this->id.' AND game_id=games.id LIMIT 1) as presence');
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
        if(!$id){
            return null;
        }

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
        $this->isNewRecord = $this->getIsNewRecord();

        if($this->isNewRecord) {
            $this->password = Yii::$app->security->generatePasswordHash($this->password);
        }
        return parent::beforeSave($insert);
    }

    /**
     * Manage adding/removing entries to team_has_player table
     * @param bool $insert
     * @param array $changedAttrs
     */
    public function afterSave($insert, $changedAttrs)
    {
        if( $this->isNewRecord ) {
            $this->updateSecurityToken();
        }
        return parent::afterSave($insert, $changedAttrs);
    }

    /**
     * Method updates model table with new security token
     * @throws \yii\db\Exception
     */
    private function updateSecurityToken()
    {
        $this->token = (string) $this->getSecurityToken();
        Yii::$app->db->createCommand()
            ->update(self::tableName(), ['token' => $this->token], "id = $this->id")
            ->execute();
    }

    /**
     * Generates new security token
     * @return \Lcobucci\JWT\Token
     */
    private function getSecurityToken()
    {
        $signer = new Sha256();

        $token = (new Builder())->setIssuer('http://localhost') // Configures the issuer (iss claim)
            ->setAudience('http://localhost') // Configures the audience (aud claim)
            ->setIssuedAt(time()) // Configures the time that the token was issue (iat claim)
            //->setNotBefore(time() + 60) // Configures the time that the token can be used (nbf claim)
            ->setExpiration(time() + 3600) // Configures the expiration time of the token (exp claim)
            ->set('uid', $this->id) // Configures a new claim, called "uid"
            ->set('mail', $this->email)
            ->sign($signer, 'tst')
            ->getToken(); // Retrieves the generated token

        return $token;
    }

    /**
     * Wrapper for update security token method, is called when user is logged in
     */
    public function refreshToken()
    {
        $this->updateSecurityToken();
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