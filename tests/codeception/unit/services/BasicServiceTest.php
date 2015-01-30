<?php
namespace nickcv\usermanager\tests\codeception\unit\services;

use yii\codeception\TestCase;
use nickcv\usermanager\services\BasicService;

class BasicServiceTest extends TestCase
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    public function testIsSingleton()
    {
        $this->assertSame(BasicService::init(), BasicService::init());
    }
    
    public function testCanClearOldInstances()
    {
        $firstInstance = BasicService::init();
        BasicService::clearAll();
        $newInstance = BasicService::init();
        
        $this->assertNotSame($firstInstance, $newInstance);
    }

}