<?php
namespace nickcv\usermanager\tests\codeception\unit\helpers;

use yii\codeception\TestCase;
use nickcv\usermanager\helpers\ArrayHelper;

class ArrayHelperTest extends TestCase
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
    
    public function testGeneratesThePrintableVersionOfAnArray()
    {
        $printableVersion = ArrayHelper::printForFile([
           'level1' => [
               'testing' => 'ok',
               'level2' => [
                   'one' => 12,
                   'two' => '12.3',
                   'three' => 'variable',
                   'level3' => [
                       'yetagain' => 'go',
                       'phpContent' => ArrayHelper::PHP_CONTENT . '\nickcv\usermanager\enums\Scenarios::LOGIN',
                   ],
               ],
           ] 
        ]);
        
        file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . 'testFile.php', $printableVersion);
        
        $this->assertFileEquals(\Yii::getAlias('@app/data/arrayHelperTest.php'), __DIR__ . DIRECTORY_SEPARATOR . 'testFile.php');
        
        unlink(__DIR__ . DIRECTORY_SEPARATOR . 'testFile.php');
    }

}