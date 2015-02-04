<?php

// NOTE: Make sure this file is not accessible when deployed to production
if (!in_array(@$_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1'])) {
    die('You are not allowed to access this file.');
}

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'test');
defined('VENDOR_DIR') or define('VENDOR_DIR', dirname(dirname(dirname(dirname(dirname(__DIR__))))));

require(VENDOR_DIR . DIRECTORY_SEPARATOR . 'autoload.php');
require(VENDOR_DIR . DIRECTORY_SEPARATOR . 'yiisoft' . DIRECTORY_SEPARATOR . 'yii2' . DIRECTORY_SEPARATOR . 'Yii.php');

$config = require(dirname(__DIR__) . '/config/acceptance.php');

(new yii\web\Application($config))->run();
