<?php
namespace nickcv\usermanager\tests\codeception\unit\fixtures;

use yii\test\ActiveFixture;

class UserFixture extends ActiveFixture
{
    public $modelClass = 'nickcv\usermanager\models\User';
    
    public $depends = ['nickcv\usermanager\tests\codeception\unit\fixtures\AuthAssignmentFixture'];
}