<?php
namespace nickcv\usermanager\tests\codeception\unit\services;

use yii\codeception\TestCase;
use nickcv\usermanager\services\EnumFilesService;

class EnumFilesServiceTest extends TestCase
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
        EnumFilesService::clearAll();
    }

    protected function _after()
    {
    }
    
    public function testClassToExtendMustExist()
    {
        $this->assertFalse(EnumFilesService::init()->updateEnum('TestEnum', ['MADEUP' => 'madeup'], '\Madeup'));
        $errors = EnumFilesService::init()->errors();
        $this->assertArrayHasKey('message', $errors);
        $this->assertEquals('The class to extend "\Madeup" does not exist.', $errors['message']);
    }
    
    public function testNamespaceAliasMustBeValid()
    {
        $this->assertFalse(EnumFilesService::init()->updateEnum('TestEnum', ['MADEUP' => 'madeup'], null, 'madeup\enums'));
        $errors = EnumFilesService::init()->errors();
        $this->assertArrayHasKey('message', $errors);
        $this->assertEquals('Invalid path alias: @madeup/enums', $errors['message']);
    }
    
    public function testConstantNamesCannotStartWithNumbers()
    {
        $this->assertFalse(EnumFilesService::init()->updateEnum('TestEnum', ['1MADEUP' => 'madeup']));
        $errors = EnumFilesService::init()->errors();
        $this->assertArrayHasKey('message', $errors);
        $this->assertEquals('Constant names cannot start with numbers, "1MADEUP" given.', $errors['message']);
    }
    
    public function testConstantNamesHaveToBeStrings()
    {
        $this->assertFalse(EnumFilesService::init()->updateEnum('TestEnum', [false => 'madeup']));
        $errors = EnumFilesService::init()->errors();
        $this->assertArrayHasKey('message', $errors);
        $this->assertEquals('Constant names should be a string, "integer" given.', $errors['message']);
    }

    public function testCreateNewEnum()
    {   
        $this->assertTrue(EnumFilesService::init()->updateEnum('TestEnum', [
            'MADEUP_ONE' => true,
            'MadeUp Two' => 'madeup',
        ], '\nickcv\usermanager\enums\BasicEnum', 'nickcv\usermanager\enums'));
        
        $this->assertFileExists(\Yii::getAlias('@nickcv/usermanager/enums/TestEnum.php'));
        $this->assertTrue(class_exists('\nickcv\usermanager\enums\TestEnum'));
        
        $reflection = new \ReflectionClass('\nickcv\usermanager\enums\TestEnum');
        $constants = $reflection->getConstants();
        
        unlink(\Yii::getAlias('@nickcv/usermanager/enums/TestEnum.php'));
        
        $this->assertCount(2, $constants);
        $this->assertArrayhasKey('MADEUP_ONE', $constants);
        $this->assertEquals(true, $constants['MADEUP_ONE']);
        $this->assertArrayhasKey('MADE_UP_TWO', $constants);
        $this->assertEquals('madeup', $constants['MADE_UP_TWO']);
    }
    
    public function testUpdateEnum()
    {
        $this->assertTrue(EnumFilesService::init()->updateEnum('TestEnum', [
            'MADEUP_ONE' => true,
            'MadeUp Two' => 'madeup',
        ], '\nickcv\usermanager\enums\BasicEnum', 'nickcv\usermanager\enums'));
        
        $this->assertTrue(EnumFilesService::init()->updateEnum('TestEnum', [
            'MADEUP_ONE' => true,
            'MadeUp Two' => 'newValue',
            'MADEUP_THREE' => 'thirdValue',
        ], '\nickcv\usermanager\enums\BasicEnum', 'nickcv\usermanager\enums'));
        
        $this->assertFileExists(\Yii::getAlias('@nickcv/usermanager/enums/TestEnum.php'));
        $this->assertTrue(class_exists('\nickcv\usermanager\enums\TestEnum'));
        $this->assertFileEquals(\Yii::getAlias('@app/data/TestEnum.php'), \Yii::getAlias('@nickcv/usermanager/enums/TestEnum.php'));
        
        unlink(\Yii::getAlias('@nickcv/usermanager/enums/TestEnum.php'));
    }
    
    public function testUpdateEnumDoesNotOverrideConstants()
    {
        $this->assertTrue(EnumFilesService::init()->updateEnum('TestEnumUnique', [
            'MADEUP_ONE' => true,
            'MadeUp Two' => 'madeup',
        ], '\nickcv\usermanager\enums\Permissions', 'nickcv\usermanager\enums'));
        
        $this->assertTrue(EnumFilesService::init()->updateEnum('TestEnumUnique', [
            'MADEUP_ONE' => true,
            'MadeUp Two' => 'newValue',
            'MADEUP_THREE' => 'thirdValue',
        ], '\nickcv\usermanager\enums\Permissions', 'nickcv\usermanager\enums'));
        
        $this->assertFileExists(\Yii::getAlias('@nickcv/usermanager/enums/TestEnumUnique.php'));
        $this->assertTrue(class_exists('\nickcv\usermanager\enums\TestEnumUnique'));
        $this->assertFileEquals(\Yii::getAlias('@app/data/TestEnumUnique.php'), \Yii::getAlias('@nickcv/usermanager/enums/TestEnumUnique.php'));
        
        unlink(\Yii::getAlias('@nickcv/usermanager/enums/TestEnumUnique.php'));
    }
    
    public function testEnumExists()
    {
        $this->assertTrue(EnumFilesService::init()->fileExists('Roles', 'nickcv\usermanager\enums'));
        $this->assertFalse(EnumFilesService::init()->fileExists('MadeUp', 'nickcv\usermanager\enums'));
    }

}