<?php

return [
    'class' => '\nickcv\usermanager\Module',
    'passwordStrength' => \nickcv\usermanager\enums\PasswordStrength::MEDIUM,
    'registration' => \nickcv\usermanager\enums\Registration::CAPTCHA,
    'activation' => \nickcv\usermanager\enums\GeneralSettings::ENABLED,
    'passwordRecovery' => \nickcv\usermanager\enums\GeneralSettings::ENABLED,
];