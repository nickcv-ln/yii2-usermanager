<?php
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use nickcv\usermanager\enums\UserStatus;

/* @var $this yii\web\View */
/* @var $model nickcv\usermanager\models\User */

$this->title = $model->email . ' | Users | Admin Panel | '.\Yii::$app->name;
$this->params['breadcrumbs'][] = 'Admin Panel';
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['admin/users']];
$this->params['breadcrumbs'][] = $model->email;

?>

<h1><?php echo $model->getFullName(); ?> [<?php echo $model->email; ?>]</h1>

<dl>
    <dt>Registration Date</dt>
    <dd><?php echo $model->registration_date; ?></dd>
    <dt>Status</dt>
    <dd><?php echo UserStatus::getLabel($model->status); ?></dd>
</dl>

<?php echo $this->render('_userRolesAndPermissions', ['model' => $model]); ?>

<h2>Login Logs</h2>

<?php echo GridView::widget([
    'dataProvider' => new ArrayDataProvider([
        'allModels' => $model->logs,
    ]),
    'columns' => [
        [
            'attribute' => 'login_date',
            'format' => ['date', 'php:d F Y H:i:s'],
        ],
        'ip',
    ],
    'layout' => '{items}',
]); 
