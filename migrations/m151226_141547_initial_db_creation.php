<?php

use yii\db\Migration;

class m151226_141547_initial_db_creation extends Migration
{
    public function up()
    {
        $tables = Yii::$app->db->schema->getTableNames();
        $dbType = $this->db->driverName;
        $tableOptions_mysql = "CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB";

        /* MYSQL */
        if (!in_array('game_has_player', $tables))  {
            if ($dbType == "mysql") {
                $this->createTable('{{%game_has_player}}', [
                    'game_id' => 'INT(11) NOT NULL',
                    'player_id' => 'INT(11) NOT NULL',
                    'presence' => 'TINYINT(4) NOT NULL',
                    'PRIMARY KEY(game_id, player_id)'
                ], $tableOptions_mysql);
            }
        }

        /* MYSQL */
        if (!in_array('games', $tables))  {
            if ($dbType == "mysql") {
                $this->createTable('{{%games}}', [
                    'id' => 'INT(11) NOT NULL AUTO_INCREMENT',
                    0 => 'PRIMARY KEY (`id`)',
                    'team_id' => 'INT(11) NOT NULL',
                    'title' => 'VARCHAR(100) NOT NULL DEFAULT \'training\'',
                    'datetime' => 'DATETIME NOT NULL',
                    'location' => 'VARCHAR(100) NOT NULL',
                ], $tableOptions_mysql);
            }
        }

        /* MYSQL */
        if (!in_array('players', $tables))  {
            if ($dbType == "mysql") {
                $this->createTable('{{%players}}', [
                    'id' => 'INT(11) NOT NULL AUTO_INCREMENT',
                    0 => 'PRIMARY KEY (`id`)',
                    'email' => 'VARCHAR(50) NOT NULL',
                    'password' => 'VARCHAR(100) NOT NULL',
                    'token' => 'VARCHAR(250) NOT NULL',
                    'name' => 'VARCHAR(50) NOT NULL',
                    'created_at' => 'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ',
                ], $tableOptions_mysql);
            }
        }

        /* MYSQL */
        if (!in_array('team_has_player', $tables))  {
            if ($dbType == "mysql") {
                $this->createTable('{{%team_has_player}}', [
                    'team_id' => 'INT(11) NOT NULL',
                    'player_id' => 'INT(11) NOT NULL',
                    'is_capitan' => 'TINYINT(4) NOT NULL',
                    'PRIMARY KEY (`team_id`,`player_id`)'
                ], $tableOptions_mysql);
            }
        }

        /* MYSQL */
        if (!in_array('teams', $tables))  {
            if ($dbType == "mysql") {
                $this->createTable('{{%teams}}', [
                    'id' => 'INT(11) NOT NULL AUTO_INCREMENT',
                    0 => 'PRIMARY KEY (`id`)',
                    'sport' => 'ENUM(\'football\',\'basketball\',\'voleyball\') NOT NULL DEFAULT \'football\'',
                    'name' => 'VARCHAR(50) NOT NULL',
                ], $tableOptions_mysql);
            }
        }


        $this->createIndex('idx_team_id_0455_00','games','team_id',0);
        $this->createIndex('idx_UNIQUE_email_0507_01','players','email',1);
        $this->createIndex('idx_UNIQUE_token_0507_02','players','token',1);
        $this->createIndex('idx_UNIQUE_sport_name_059_03','teams',['sport','name'],1);

        $this->execute('SET foreign_key_checks = 0');
        $this->addForeignKey('fk_games_0402_00','{{%game_has_player}}', 'game_id', '{{%games}}', 'id', 'CASCADE', 'NO ACTION' );
        $this->addForeignKey('fk_teams_0447_01','{{%games}}', 'team_id', '{{%teams}}', 'id', 'CASCADE', 'NO ACTION' );
        $this->addForeignKey('fk_teams_0539_02','{{%team_has_player}}', 'team_id', '{{%teams}}', 'id', 'CASCADE', 'NO ACTION' );
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        $this->execute('DROP TABLE IF EXISTS `game_has_player`');
        $this->execute('SET foreign_key_checks = 1;');
        $this->execute('SET foreign_key_checks = 0');
        $this->execute('DROP TABLE IF EXISTS `games`');
        $this->execute('SET foreign_key_checks = 1;');
        $this->execute('SET foreign_key_checks = 0');
        $this->execute('DROP TABLE IF EXISTS `players`');
        $this->execute('SET foreign_key_checks = 1;');
        $this->execute('SET foreign_key_checks = 0');
        $this->execute('DROP TABLE IF EXISTS `team_has_player`');
        $this->execute('SET foreign_key_checks = 1;');
        $this->execute('SET foreign_key_checks = 0');
        $this->execute('DROP TABLE IF EXISTS `teams`');
        $this->execute('SET foreign_key_checks = 1;');
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
