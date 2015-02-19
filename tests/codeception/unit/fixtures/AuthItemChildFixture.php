<?php
namespace nickcv\usermanager\tests\codeception\unit\fixtures;

use yii\test\ActiveFixture;

class AuthItemChildFixture extends ActiveFixture
{
    public $tableName = 'auth_item_child';
    
    public $depends = ['nickcv\usermanager\tests\codeception\unit\fixtures\AuthItemFixture'];
}