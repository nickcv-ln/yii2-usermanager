<?php
namespace nickcv\usermanager\tests\codeception\unit\fixtures;

use yii\test\ActiveFixture;

class UserLogsFixture extends ActiveFixture
{
    public $modelClass = 'nickcv\usermanager\models\UserLogs';
    public $depends = ['nickcv\usermanager\tests\codeception\unit\fixtures\UserFixture'];
}