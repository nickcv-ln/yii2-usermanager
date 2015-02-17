<?php

use nickcv\usermanager\helpers\AuthHelper;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model yii\rbac\Role */
?>

<h3><kbd><?php echo $model->name; ?></kbd> permissions (includes inherited ones).</h3>

<?php echo GridView::widget([
    'dataProvider' => AuthHelper::getAllPermissions($model->name, true),
    'columns' => [
        'name',
        'description',
        [
            'attribute' => 'createdAt',
            'format' => ['date', 'php:Y-m-d'],
        ],
        [
            'attribute' => 'updatedAt',
            'format' => ['date', 'php:Y-m-d'],
        ],
    ],
]);