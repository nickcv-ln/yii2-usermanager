<?php
use yii\grid\GridView;
use yii\data\ArrayDataProvider;
use nickcv\usermanager\helpers\AuthHelper;

/* @var $this yii\web\View */
/* @var $model nickcv\usermanager\models\User */
?>

<h2>Roles</h2>
<?php foreach (\Yii::$app->authManager->getRolesByUser($model->id) as $role): ?>
<h3><kbd><?php echo $role->name; ?></kbd> permissions</h3>
<?php echo GridView::widget([
    'dataProvider' => AuthHelper::getDirectPermissions($role->name, true),
    'layout' => '{items}',
    'emptyText' => 'this role does not have any direct permission.',
    'columns' => [
        'name',
        'description',
    ],
]); ?>
<?php endforeach;
