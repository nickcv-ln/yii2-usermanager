<?php
namespace nickcv\usermanager;

use yii\web\AssetBundle as AB;

class AssetBundle extends AB 
{
    public $sourcePath = '@nickcv/usermanager/assets'; 
    public $css = [ 
        'css/usermanager.css', 
    ]; 
}