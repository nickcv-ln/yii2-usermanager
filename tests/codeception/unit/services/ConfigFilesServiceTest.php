<?php
namespace nickcv\usermanager\tests\codeception\unit\services;

use yii\codeception\TestCase;
use nickcv\usermanager\services\ConfigFilesService;

class ConfigFilesServiceTest extends TestCase
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
        ConfigFilesService::clearAll();
    }

    protected function _after()
    {
    }

    public function testChecksIfAConfigFileExistsInAppScope()
    {
        $this->assertTrue(ConfigFilesService::init()->fileExists('web.php'));
        $this->assertFalse(ConfigFilesService::init()->fileExists('madeup.php'));
    }
    
    public function testChecksIfAConfigFileExistsInTestsScope()
    {
        \Yii::setAlias('@codeception', '@tests/codeception');
        
        $this->assertTrue(ConfigFilesService::init()->fileExists('acceptance.php', '@codeception'));
        $this->assertFalse(ConfigFilesService::init()->fileExists('madeup.php', '@codeception'));
    }
    
    public function testCannotCreateConfigFileIfAlreadyExists()
    {
        $this->assertFalse(ConfigFilesService::init()->createFile('web.php'));
        $errors = ConfigFilesService::init()->errors();
        $this->assertArrayHasKey('message', $errors);
        $this->assertEquals('The configuration file "web.php" already exists in the given scope "@app". To update a file please use the nickcv\usermanager\services\ConfigFilesService::updateFile() method.', $errors['message']);
    }
    
    public function testCannotCreateFileIfDirectoryDoesNotExist()
    {
        $this->assertFalse(ConfigFilesService::init()->createFile('madeup.php', [
            'passwordSecurity' => 12,
        ], '/etc'));
        $errors = ConfigFilesService::init()->errors();
        $this->assertArrayHasKey('message', $errors);
        $this->assertEquals('The file "/etc/config/madeup.php" could not be written. For more informations check the details.', $errors['message']);
        $this->assertArrayHasKey('details', $errors);
        $this->assertArrayHasKey('message', $errors['details']);
        $this->assertEquals('file_put_contents(/etc/config/madeup.php): failed to open stream: No such file or directory', $errors['details']['message']);
        $this->assertArrayHasKey('file', $errors['details']);
        
        $serviceClassPath = dirname(dirname(dirname(dirname(__DIR__)))) . DIRECTORY_SEPARATOR . 'services' . DIRECTORY_SEPARATOR . 'ConfigFilesService.php';
        
        $this->assertEquals($serviceClassPath, $errors['details']['file']);
        $this->assertArrayHasKey('line', $errors['details']);
        $this->assertEquals('162', $errors['details']['line']);
    }
    
    public function testCreateConfigFile()
    {
        $this->assertTrue(ConfigFilesService::init()->createFile('madeup.php', [
            'class'=>'\nickcv\usermanager\Module',
            'passwordSecurity' => '12',
        ]));
        $this->assertFileExists(\Yii::getAlias('@app/config/madeup.php'));
        $this->assertFileEquals(\Yii::getAlias('@app/data/configFileServiceTest.php'), \Yii::getAlias('@app/config/madeup.php'));
        unlink(\Yii::getAlias('@app/config/madeup.php'));
    }
    
    public function testCannotUpdateNotExistingFile()
    {
        $this->assertFalse(ConfigFilesService::init()->updateFile('madeup.php', []));
        $errors = ConfigFilesService::init()->errors();
        $this->assertArrayHasKey('message', $errors);
        $this->assertEquals('The configuration file "madeup.php" does not exists in the given scope "@app". To create a file please use the nickcv\usermanager\services\ConfigFilesService::createFile() method.', $errors['message']);
    }
    
    public function testDoesNotUpdateIfOverridesValues()
    {
        ConfigFilesService::init()->createFile('madeup.php', [
            'passwordSecurity' => '12',
        ]);
        
        $this->assertFalse(ConfigFilesService::init()->updateFile('madeup.php', [
            'passwordSecurity' => 40,
        ]));
        
        unlink(\Yii::getAlias('@app/config/madeup.php'));
        
        $errors = ConfigFilesService::init()->errors();
        $this->assertArrayHasKey('message', $errors);
        $this->assertEquals('Several values would be overridden. Check Details for a complete list.', $errors['message']);
        $this->assertArrayHasKey('details', $errors);
        $this->assertArrayHasKey('passwordSecurity', $errors['details']);
        $this->assertArrayHasKey('current', $errors['details']['passwordSecurity']);
        $this->assertEquals(12, $errors['details']['passwordSecurity']['current']);
        $this->assertArrayHasKey('afterUpdate', $errors['details']['passwordSecurity']);
        $this->assertEquals(40, $errors['details']['passwordSecurity']['afterUpdate']);
    }
    
    public function testUpdatesIfDoesNotOverridesValues()
    {
        ConfigFilesService::init()->createFile('madeup.php', [
            'passwordSecurity' => '12',
        ]);
        
        $this->assertTrue(ConfigFilesService::init()->updateFile('madeup.php', [
            'other' => 'test',
        ]));
        
        $newConfig = require(\Yii::getAlias('@app/config/madeup.php'));
        
        unlink(\Yii::getAlias('@app/config/madeup.php'));

        $this->assertArrayHasKey('passwordSecurity', $newConfig);
        $this->assertEquals(12, $newConfig['passwordSecurity']);
        $this->assertArrayHasKey('other', $newConfig);
        $this->assertEquals('test', $newConfig['other']);
    }
    
    public function testUpdateIfCheckIsSkipped()
    {
        ConfigFilesService::init()->createFile('madeup.php', [
            'passwordSecurity' => '12',
        ]);
        
        $this->assertTrue(ConfigFilesService::init()->updateFile('madeup.php', [
            'passwordSecurity' => '40',
        ], true));
        
        $newConfig = require(\Yii::getAlias('@app/config/madeup.php'));
        
        unlink(\Yii::getAlias('@app/config/madeup.php'));

        $this->assertArrayHasKey('passwordSecurity', $newConfig);
        $this->assertEquals(40, $newConfig['passwordSecurity']);
    }
    
    public function testGetNotExistingConfigFile()
    {
        $this->assertEquals([], ConfigFilesService::init()->getConfigFile('madeup.php'));
    }
    
    public function testGetExistingConfigFile()
    {
        $array = [
            'class' => '\nickcv\usermanager\Module',
            'passwordStrength' => 2,
            'registration' => 'captcha',
            'activation' => 'enabled',
            'passwordRecovery' => 'enabled',
        ];
        $this->assertEquals($array, ConfigFilesService::init()->getConfigFile('usermanager.php'));
    }

}