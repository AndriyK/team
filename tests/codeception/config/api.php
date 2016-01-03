<?php
/**
 * Application configuration for unit tests
 */
return yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/../../../config/api.php'),
    require(__DIR__ . '/config.php'),
    [

    ]
);
