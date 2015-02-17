<?php
use yii\grid\GridView;
use yii\widgets\ListView;
use nickcv\usermanager\Module;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $permissionForm nickcv\usermanager\forms\PermissionForm */
/* @var $directPermissions yii\data\ArrayDataProvider */
/* @var $childrenRoles yii\data\ArrayDataProvider */

$this->title = $permissionForm->role . ' | Roles | Admin Panel | '.\Yii::$app->name;
$this->params['breadcrumbs'][] = 'Admin Panel';
$this->params['breadcrumbs'][] = ['label' => 'Roles', 'url' => ['admin/roles']];
$this->params['breadcrumbs'][] = $permissionForm->role;

?>

<div class="jumbotron usermanager">
    <h1>Roles and Permissions</h1>
    <p>From here you can manage roles and permissions.</p>
    <p>
        The Enum files <kbd>\app\enums\<?php echo Module::EXTENDED_PERMISSIONS_CLASS; ?></kbd>
        and <kbd>\app\enums\<?php echo Module::EXTENDED_ROLES_CLASS ?></kbd> 
        will automatically contain constants for each role and permission you 
        create, to avoid the use of <em>Magic Words</em> throughout the application.
    </p>
</div>

<?php echo $this->render('_nav', ['activateRoles' => true]); ?>

<div class="col-lg-12 push-down-30">
    <h2>Direct permissions of the <kbd><?php echo $permissionForm->role; ?></kbd> role.</h2>
    <hr>
    <?php echo GridView::widget([
        'dataProvider' => $directPermissions,
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
    
    <h2>Children roles of <kbd><?php echo $permissionForm->role; ?></kbd></h2>
    <p>To edit the permission of a child role go to that role page by clicking on its name.</p>
    <?php echo ListView::widget([
        'dataProvider' => $childrenRoles,
        'itemView' => '_childrenRoles',
        'layout' => '{items}',
    ]); ?>
    
    <hr>
    
    <?php echo $this->render('_permissionModal', ['permissionForm' => $permissionForm]); ?>
    
</div>
