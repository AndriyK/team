<?php

use yii\db\Migration;

class m160122_151400_alter_token_to_text_type extends Migration
{
    public function up()
    {
        $this->dropIndex('idx_UNIQUE_token_0507_02', 'players');
        $this->alterColumn('players', 'token', 'TEXT NOT NULL');
        $this->createIndex('idx_token_0507_02', 'players', '`token` ( 250 )', 0);
    }

    public function down()
    {
        echo "m160122_151400_alter_token_to_text_type can not be reverted";
        return false;
    }
}