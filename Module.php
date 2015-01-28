<?php
/**
 * Contains the module class used by the whole yii2-usermanager module.
 * 
 * @link http://www.creationgears.com/
 * @copyright Copyright (c) 2015 Nicola Puddu
 * @license http://www.gnu.org/copyleft/gpl.html
 * @package nickcv/yii2-usermanager
 * @author Nicola Puddu <n.puddu@outlook.com>
 */
namespace nickcv\usermanager;

use yii\base\BootstrapInterface;

/**
 * This class defines the usermanager module.
 * 
 * @author Nicola Puddu <n.puddu@outlook.com>
 * @version 1.0
 */
class Module extends \yii\base\Module implements BootstrapInterface
{   
    public function init()
    {   
        parent::init();
    }
    
    /**
     * @param \yii\base\Application $app
     */
    public function bootstrap($app)
    {
        
        if ($app instanceof \yii\console\Application) {
            $this->setAliases([
                'nickcv' =>  dirname(__FILE__),
            ]);
            
            $app->controllerMap[$this->id] = [
                'class' => 'nickcv\usermanager\commands\SetupController',
            ];
        }
    }
}