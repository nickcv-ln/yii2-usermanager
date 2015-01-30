<?php
namespace nickcv\usermanager\tests\codeception\unit\helpers;

use yii\codeception\TestCase;
use nickcv\usermanager\helpers\StringHelper;


class StringHelperTest extends TestCase
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
    
    /**
     * @expectedException        \yii\base\InvalidParamException
     * @expectedExceptionMessage $lenght should be an integer, "double" given.
     */
    public function testRandomStringParameterMustBeAnInteger()
    {
        StringHelper::randomString(9.2);
    }

    /**
     * @expectedException        \yii\base\InvalidParamException
     * @expectedExceptionMessage $lenght should not be less then 8, "7" given.
     */
    public function testCannotGetRandomStringWithLessThan8Characters()
    {
        StringHelper::randomString(7);
    }
    
    public function testGetRandomString()
    {
        $string = StringHelper::randomString();
        $this->assertEquals(12, strlen($string));
        $matches = null;
        preg_match('/^(?=.*[\W_](?=.*[\W_](?=.*[\W_](?=.*[\W_])))).{4,}$/', $string, $matches);
        $this->assertCount(1, $matches);
        $this->assertTrue(true);
    }

}