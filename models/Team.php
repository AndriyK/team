<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "teams".
 *
 * @property integer $id
 * @property string $sport
 * @property string $name
 */
class Team extends \yii\db\ActiveRecord
{
    public $join_player;
    public $is_capitan;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'teams';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sport'], 'string'],
            [['sport'], 'in', 'range' => ['football', 'backetball', 'voleyball','']],
            [['name'], 'required'],
            [['name'], 'string', 'max' => 50],
            [['sport', 'name'], 'unique', 'targetAttribute' => ['sport', 'name'], 'message' => 'The combination of Sport and Name has already been taken.'],
            [['join_player'], 'safe'],
            [['join_player'], 'email'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sport' => 'Sport',
            'name' => 'Name',
        ];
    }

    public function getPlayers()
    {
        return $this->hasMany(Player::className(), ['id' => 'player_id'])
            ->viaTable('team_has_player', ['team_id' => 'id'])
            ->select('*, (SELECT is_capitan FROM team_has_player WHERE team_id='.$this->id.' AND player_id=players.id LIMIT 1) as is_capitan');
    }

    public function fields()
    {
        return ['id', 'sport', 'name', 'is_capitan'];
    }

    public function extraFields()
    {
        return ['players'];
    }

    public function afterSave($insert, $changedAttrs)
    {
        parent::afterSave($insert, $changedAttrs);
        if($this->getIsNewRecord()) {
            $this->link('players', Yii::$app->user->getIdentity(false), array('is_capitan' => 1));
        } elseif(!empty($this->join_player) && ($player = Player::findByMail($this->join_player))){
            $this->link('players', $player);
        }
    }

    public function afterDelete()
    {
        parent::afterDelete();
        $this->unlink('players', Yii::$app->user->getIdentity(false), true);
    }

    public function toArray(array $fields = [], array $expand = [], $recursive = true)
    {
        return array_filter(parent::toArray($fields,$expand,$recursive), function($val){
            return is_null($val) ? false : true;
        });
    }
}
