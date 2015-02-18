<?php

use nickcv\usermanager\helpers\AuthHelper;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model yii\rbac\Role */
/* @var $roleForm nickcv\usermanager\forms\RoleForm */

$roleForm->name = $model->name;
?>

<h3 class="push-down-40"><kbd><?php echo Html::a($model->name, ['admin/roles/' . $model->name]); ?></kbd> direct permissions.</h3>
<?php echo $this->render('_revokeRoleForm', ['model' => $roleForm]); ?>

<?php echo GridView::widget([
    'dataProvider' => AuthHelper::getDirectPermissions($model->name, true),
    'emptyText' => 'this role does not have any direct permission.',
    'layout' => '{items}',
    'columns' => [
        'name',
        'description',
        [
            'attribute' => 'createdAt',
            'format' => ['date', 'php:Y-m-d H:m'],
        ],
        [
            'attribute' => 'updatedAt',
            'format' => ['date', 'php:Y-m-d H:m'],
        ],
    ],
    'options' => [
        'class' => 'grid-view push-down-30',
    ],
]);

$roleForm->name = null;
