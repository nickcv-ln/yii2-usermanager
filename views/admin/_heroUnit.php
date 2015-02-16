<?php
use nickcv\usermanager\AssetBundle;
use yii\bootstrap\Nav;
use yii\bootstrap\Alert;

/* @var $this yii\web\View */

AssetBundle::register($this);
?>
<div class="jumbotron usermanager">
    <h1>Admin Panel</h1>
    <p>
        This is the <em>usermanager admin panel</em>, from here you can 
        change the module configuration's, create and edit users and manage
        roles.
    </p>
</div>

<div class="clearfix"></div>
<?php if (\Yii::$app->session->hasFlash('success')):
    echo Alert::widget([
        'options' => [
            'class' => 'alert-success',
        ],
        'body' => \Yii::$app->session->getFlash('success'),
    ]);
endif; ?>

<div class="col-lg-12">
    <?php echo Nav::widget([
        'items' => [
            [
                'label' => 'Configuration',
                'url' => ['admin/configuration'],
            ],
            [
                'label' => 'Users',
                'url' => ['admin/users'],
            ],
            [
                'label' => 'Roles',
                'url' => ['admin/roles'],
            ],
        ],
        'options' => [
            'class' => 'nav-tabs nav-justified',
        ],
    ]); ?>
</div>