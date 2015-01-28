<?php
/**
 * Application configuration for acceptance tests
 */
return yii\helpers\ArrayHelper::merge(
    require(__DIR__ . DIRECTORY_SEPARATOR . 'web.php'),
    require(__DIR__ . DIRECTORY_SEPARATOR . 'config.php'),
    [

    ]
);
