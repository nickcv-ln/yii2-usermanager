User Manager for Yii2
======================
User Manager for Yii2
Version 0.1.0

This module is used for complete users, roles and permissions management.

**This is currently in development and is not meant to be used by any means**


Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist nickcv/yii2-usermanager "*"
```

or add

```
"nickcv/yii2-usermanager": "*"
```

to the require section of your `composer.json` file.


Set Up
------

The module comes with a console command to install it.
Before executing the command you will have to apply the following changes to a few configuration files.

___
**Required changes to ```web.php```**

The ```user``` component must use the identity class that comes with the module and you have to change the loginUrl as follows:

```
'user' => [
    'identityClass' => 'nickcv\usermanager\models\User',
    'enableAutoLogin' => true,
    'loginUrl' => ['/usermanager/login'],
],
```

The ```authManager``` should be configure to use the database:

```
'authManager' => [
    'class' => 'yii\rbac\DbManager',
],
```

___
**Required changes to ```console.php```**

The usermanager module must be configured with the basic configuration to execute the install console command:

```
'modules' => [
    'usermanager' => '\nickcv\usermanager\Module',
],
```

Usermanager must then be added to the list of bootstrapped  modules:

```
'bootstrap' => ['log', 'usermanager'],
```

Just like in the ```web.php``` file the ```authManager``` component must be configured to use the database:

```
'authManager' => [
    'class' => 'yii\rbac\DbManager',
],
```

___
**Installing the Module**

You should now be able to execute the ```./yii usermanager/install``` command.

Follow the instruction on screen and **don't forget to change the usermanager configuration** using the config file generated automatically.

Console Commands
----------------

The module comes with other console commands.
To list them all just execute the ```./yii``` command

```./yii usermanager/create-admin```: creates a new admin user within the system.


Testing
------------

The entire module was built with TDD.

To execute the acceptance tests make sure to create a vhost with ```SeverName yii2-nickcv.test``` and the document root pointing at ```vendor/nickcv/yii2-usermanager/tests/codeception/web```

To launch the tests just go inside the extension directory and execute the ```codecept run``` command.