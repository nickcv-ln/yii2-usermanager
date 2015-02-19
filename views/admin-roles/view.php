<?php
use yii\grid\GridView;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $permissionForm nickcv\usermanager\forms\PermissionForm */
/* @var $roleForm nickcv\usermanager\forms\RoleForm */
/* @var $directPermissions yii\data\ArrayDataProvider */
/* @var $childrenRoles yii\data\ArrayDataProvider */

$this->title = $permissionForm->role . ' | Roles | Admin Panel | '.\Yii::$app->name;
$this->params['breadcrumbs'][] = 'Admin Panel';
$this->params['breadcrumbs'][] = ['label' => 'Roles', 'url' => ['admin/roles']];
$this->params['breadcrumbs'][] = $permissionForm->role;

?>

<h1>Direct permissions of the <kbd><?php echo $permissionForm->role; ?></kbd> role.</h1>
<hr>
<?php echo GridView::widget([
    'dataProvider' => $directPermissions,
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
        [
            'class' => yii\grid\ActionColumn::className(),
            'template' => '{delete}',
            'buttons' => [
                'delete' => function ($url, $model, $key) use ($permissionForm) {
                    return \Yii::$app->controller->renderPartial('_revokePermissionForm', ['permission' => $model, 'model' => $permissionForm]);
                }
            ],
        ],
    ],
]); ?>

<h2 class="push-down-50">Children roles of <kbd><?php echo $permissionForm->role; ?></kbd></h2>
<p>To edit the permission of a child role go to that role page by clicking on its name.</p>
<hr>
<?php echo ListView::widget([
    'dataProvider' => $childrenRoles,
    'itemView' => '_childrenRoles',
    'layout' => '{items}',
    'viewParams' => ['roleForm' => $roleForm],
]); ?>

<hr>

<?php echo $this->render('_permissionModal', ['permissionForm' => $permissionForm]); ?>

<?php echo $this->render('_addRoleModal', ['model' => $roleForm]); ?>
