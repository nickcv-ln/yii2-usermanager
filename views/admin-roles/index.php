<?php
use yii\helpers\Html;
use yii\widgets\Pjax;
use nickcv\usermanager\Module;

/* @var $this yii\web\View */
/* @var $roleForm nickcv\usermanager\forms\RoleForm */

$this->title = 'Roles | Admin Panel | '.\Yii::$app->name;
$this->params['breadcrumbs'][] = 'Admin Panel';
$this->params['breadcrumbs'][] = 'Roles';

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

    <?php Pjax::begin(); ?>
    <?php echo yii\grid\GridView::widget([
        'dataProvider' => $roles,
        'columns' => [
            [
                'attribute' => 'name',
                'format' => 'raw',
                'value' => function($model, $key) {
                    return Html::a($key, ['admin/roles/' . $key], ['data-pjax' => 0]);
                }
            ],
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
    ]); ?>
    <?php Pjax::end(); ?>
<hr>

<?php echo $this->render('_newRoleModal', ['model' => $roleForm]); ?>
