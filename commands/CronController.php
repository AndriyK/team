<?php
namespace app\commands;

use yii\console\Controller;

class CronController extends Controller
{
    /**
     * clean app database from old entries
     * - remove all passed games, table games, where datetime < NOW
     * - remove all tests accounts (from e2e tests), table players, where email LIKE "test%@test.test"
     */
    public function actionCleanDb()
    {
        $this->removeOldGames();
        $this->removeTestPlayers();

        echo "OK\n";
    }

    private function removeOldGames()
    {
        $sql = "DELETE FROM `games` WHERE `datetime` < NOW()";
        \Yii::trace("******** Delete old games: {$sql}");

        $res = \Yii::$app->db->createCommand($sql)->execute();
        \Yii::trace("Deleted _{$res}_ games");
    }

    private function removeTestPlayers()
    {
        $sql = "DELETE FROM `players` WHERE `email` LIKE 'test1%@test.test'";
        \Yii::trace("******** Delete test players: {$sql}");

        $res = \Yii::$app->db->createCommand($sql)->execute();
        \Yii::trace("Deleted _{$res}_ players");
    }
}
