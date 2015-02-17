<?php

use nickcv\usermanager\services\ConfigFilesService;
use nickcv\usermanager\Module;
use nickcv\usermanager\helpers\ArrayHelper as AH;

new yii\web\Application(require(dirname(__DIR__) . '/config/functional.php'));

ConfigFilesService::init()->createFile(Module::CONFIG_FILENAME, [
    'class' => '\nickcv\usermanager\Module',
    'passwordStrength' => AH::PHP_CONTENT . '\nickcv\usermanager\enums\PasswordStrength::MEDIUM',
    'registration' => AH::PHP_CONTENT . '\nickcv\usermanager\enums\Registration::CAPTCHA',
    'activation' => AH::PHP_CONTENT . '\nickcv\usermanager\enums\GeneralSettings::ENABLED',
    'passwordRecovery' => AH::PHP_CONTENT . '\nickcv\usermanager\enums\GeneralSettings::ENABLED', 
]);
