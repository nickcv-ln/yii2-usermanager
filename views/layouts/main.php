<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use nickcv\usermanager\AssetBundle;
use nickcv\usermanager\enums\Permissions;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
AssetBundle::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?php echo Yii::$app->language; ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php echo Html::csrfMetaTags(); ?>
    <title><?= Html::encode($this->title); ?></title>
    <?php $this->head(); ?>
</head>
<body>

<?php $this->beginBody(); ?>
    <div class="wrap">
        <?php
            NavBar::begin([
                'brandLabel' => Yii::$app->name,
                'brandUrl' => Yii::$app->homeUrl,
                'options' => [
                    'class' => 'navbar-inverse navbar-fixed-top',
                ],
            ]);
            echo Nav::widget([
                'options' => ['class' => 'navbar-nav navbar-right'],
                'items' => [
                    [
                        'label' => 'Users',
                        'url' => ['admin-users/index'],
                        'active' => \Yii::$app->controller->id === 'admin-users',
                        'visible' => \Yii::$app->authManager->checkAccess(\Yii::$app->user->id, Permissions::USER_MANAGEMENT),
                    ],
                    [
                        'label' => 'Roles',
                        'url' => ['admin-roles/index'],
                        'active' => \Yii::$app->controller->id === 'admin-roles',
                        'visible' => \Yii::$app->authManager->checkAccess(\Yii::$app->user->id, Permissions::ROLES_MANAGEMENT),
                    ],
                    [
                        'label' => 'Configuration',
                        'url' => ['admin-configuration/index'],
                        'active' => \Yii::$app->controller->id === 'admin-configuration',
                        'visible' => \Yii::$app->authManager->checkAccess(\Yii::$app->user->id, Permissions::MODULE_MANAGEMENT),
                    ],
                    Yii::$app->user->isGuest ?
                        ['label' => 'Login', 'url' => ['default/login']] :
                        ['label' => 'Logout (' . Yii::$app->user->identity->username . ')',
                            'url' => ['default/logout'],
                            'linkOptions' => ['data-method' => 'post']
                        ],
                ],
            ]);
            NavBar::end();
        ?>
        
        <div class="container">
            <?php echo Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]); ?>
            <?php echo $this->render('_flashMessages'); ?>
            <?php echo $content; ?>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <p class="pull-left">&copy;<?php echo \Yii::$app->name; ?> <?php echo date('Y'); ?></p>
            <p class="pull-right">powered by <?php echo Html::a('usermanager', 'https://github.com/nickcv-ln/yii2-usermanager', ['target' => '_blank', 'rel' => 'external']); ?></p>
        </div>
    </footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
