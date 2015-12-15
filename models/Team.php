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
            [['name'], 'required'],
            [['name'], 'string', 'max' => 50],
            [['sport', 'name'], 'unique', 'targetAttribute' => ['sport', 'name'], 'message' => 'The combination of Sport and Name has already been taken.'],
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
            ->viaTable('team_has_player', ['team_id' => 'id']);
    }

    public function fields()
    {
        return ['id', 'sport', 'name', /*'players'*/];
    }
}
