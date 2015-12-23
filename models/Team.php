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
class Team extends AppActiveRecord
{
    /**
     * Holds passed join_player value for adding player to the team
     * (new entry to team_has_player table)
     * @var String
     */
    public $join_player;

    /**
     * Holds passed remove_player value for removing player from the team
     * (remove entry from team_has_player table)
     * @var String
     */
    public $remove_player;

    /**
     * Helper atribute for correct view of many-to-many relation with player model
     * (shows which player is a capitan of a team)
     * @var int
     */
    public $is_capitan;

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
        return 'teams';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sport'], 'string'],
            [['sport'], 'in', 'range' => ['football', 'basketball', 'voleyball','']],
            [['name'], 'required'],
            [['name'], 'string', 'max' => 50],
            [['sport', 'name'], 'unique', 'targetAttribute' => ['sport', 'name'], 'message' => 'The combination of Sport and Name has already been taken.'],
            [['join_player','remove_player'], 'email'],
        ];
    }


    /**
     * Defines many-to-many relation with player model via table team_has_player
     * @return list of team's players
     */
    public function getPlayers()
    {
        return $this->hasMany(Player::className(), ['id' => 'player_id'])
            ->viaTable('team_has_player', ['team_id' => 'id'])
            ->select('*, (SELECT is_capitan FROM team_has_player WHERE team_id='.$this->id.' AND player_id=players.id LIMIT 1) as is_capitan');
    }

    /**
     * Defines one-to-many relation with games table
     * @return \yii\db\ActiveQuery
     */
    public function getGames()
    {
        return $this->hasMany(Game::className(), ['team_id' => 'id']);
    }

    /**
     * @inheritdoc
     */
    public function fields()
    {
        return ['id', 'sport', 'name', 'is_capitan', 'players'];
    }

    /**
     * @inheritdoc
     */
    public function extraFields()
    {
        return ['games'];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        $this->isNewRecord = $this->getIsNewRecord();
        return parent::beforeSave($insert);
    }
    
    /**
     * Manage adding/removing entries to team_has_player table
     * @param bool $insert
     * @param array $changedAttrs
     */
    public function afterSave($insert, $changedAttrs)
    {
        $this->manageTeamOwner();
        $this->manageTeamMembers();
        return parent::afterSave($insert, $changedAttrs);
    }

    /**
     * When new team added, link it with player and mark it as team capitan
     */
    private function manageTeamOwner()
    {
        if( !$this->isNewRecord ) {
            return;
        }

        $this->link('players', Yii::$app->user->getIdentity(false), array('is_capitan' => 1));
    }

    /**
     * When team is updated, link/unlink players with team
     */
    private function manageTeamMembers()
    {
        if($this->isNewRecord) {
            return;
        }

        if( $player = Player::findByMail($this->join_player) ){
            $this->link('players', $player);
        }

        if( $player = Player::findByMail($this->remove_player) ){
            $this->unlink('players', $player, true);
        }
    }
}
