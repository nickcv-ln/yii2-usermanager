<?php
use nickcv\usermanager\AssetBundle;
use yii\bootstrap\Nav;
use yii\bootstrap\Alert;

/* @var $this yii\web\View */
/* @var $activateRoles boolean */

AssetBundle::register($this);
?>

<div class="clearfix"></div>
<?php if (\Yii::$app->session->hasFlash('success')):
    echo Alert::widget([
        'options' => [
            'class' => 'alert-success',
        ],
        'body' => \Yii::$app->session->getFlash('success', null, true),
    ]);
endif;
if (\Yii::$app->session->hasFlash('error')):
    $error = \Yii::$app->session->getFlash('error', null, true);
    echo Alert::widget([
        'options' => [
            'class' => 'alert-danger',
        ],
        'body' => '<strong>' . $error['message'] . '</strong><br><br> - ' . implode('<br> - ', $error['errors']),
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
                'active' => isset($activateRoles) && $activateRoles === true ? true : null,
            ],
        ],
        'options' => [
            'class' => 'nav-tabs nav-justified',
        ],
    ]); ?>
</div>