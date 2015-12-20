<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "games".
 *
 * @property integer $id
 * @property integer $team_id
 * @property string $title
 * @property string $datetime
 * @property string $location
 */
class Game extends AppActiveRecord
{
    /**
     * Helper atribute for correct view of many-to-many relation with player model
     * (shows which player reported his presence on the game)
     * @var int
     */
    public $presence;

    /**
     * Holds passed join_player value reporting player's presence on the game
     * (entry in game_has_player table)
     * @var String
     */
    public $join_player;

    /**
     * Holds passed reject_player value reporting player's presence on the game
     * (entry in game_has_player table)
     * @var String
     */
    public $reject_player;

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
        return 'games';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['team_id', 'datetime', 'location'], 'required'],
            [['team_id'], 'integer'],
            [['team_id'], 'exist', 'targetClass'=>Team::className(), 'targetAttribute' => 'id'],
            [['datetime'], 'date', 'format' => 'php:Y-m-d H:i:s'],
            [['title', 'location'], 'string', 'max' => 100],
            [['join_player', 'reject_player'], 'integer'],
            [['join_player', 'reject_player'], 'exist', 'targetClass'=>Player::className(), 'targetAttribute' => 'id'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function fields()
    {
        return ['id', 'team_id', 'title', 'datetime', 'location', 'presence'];
    }

    /**
     * @inheritdoc
     */
    public function extraFields()
    {
        return ['team', 'players'];
    }

    /**
     * Defines one-to-many relation with Team model
     * (return team which has planned game)
     * @return \yii\db\ActiveQuery
     */
    public function getTeam()
    {
        return $this->hasOne(Team::className(), ['id' => 'team_id']);
    }

    /**
     * Defines many-to-many relation with player model via table game_has_player
     * @return list of games's players
     */
    public function getPlayers()
    {
        return $this->hasMany(Player::className(), ['id' => 'player_id'])
            ->viaTable('game_has_player', ['game_id' => 'id'])
            ->select('*, (SELECT presence FROM game_has_player WHERE game_id='.$this->id.' AND player_id=players.id LIMIT 1) as presence');
    }

    /**
     * Unlink all players when game is deleted
     */
    public function afterDelete()
    {
        parent::afterDelete();
        $this->unlinkAll('players', true);
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
        $this->managePlayersPresence();
        return parent::afterSave($insert, $changedAttrs);
    }

    /**
     * mark players psence on the game
     * @return int|void
     * @throws \yii\db\Exception
     */
    private function managePlayersPresence()
    {
        if($this->isNewRecord) {
            return;
        }

        if( $player = Player::findIdentity($this->join_player) ){
            $presence = 1;
        }elseif( $player = Player::findIdentity($this->reject_player) ){
            $presence = 0;
        } else {
            return;
        }

        $sql = "INSERT INTO `game_has_player` (`game_id`, `player_id`, `presence`)
                VALUES ('{$this->id}', '{$player->id}', '{$presence}')
                ON DUPLICATE KEY UPDATE `presence` = '{$presence}'";

        return Yii::$app->db->createCommand($sql)->execute();
    }
}
