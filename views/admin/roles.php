<?php
use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = 'Roles | Admin Panel | '.\Yii::$app->name;
$this->params['breadcrumbs'][] = 'Admin Panel';
$this->params['breadcrumbs'][] = 'Roles';

?>

<div class="jumbotron usermanager">
    <h1>Roles and Permissions</h1>
    <p>From here you can manage roles and permissions.</p>
    <p>
        The Enum files <kbd>\app\enums\ExtendedPermissions</kbd> and <kbd>\app\enums\ExtendedRoles</kbd>
        will automatically contain constants for each role and permission you create,
        to avoid the use of Magic Words throughout the application.
    </p>
</div>

<?php echo $this->render('_nav'); ?>

<div class="col-lg-12 push-down-30">
    <?php echo yii\grid\GridView::widget([
        'dataProvider' => $roles,
        'columns' => [
            [
                'attribute' => 'name',
                'format' => 'html',
                'value' => function($model, $key) {
                    return Html::a($key, ['admin/roles/' . $key]);
                }
            ],
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
</div>
