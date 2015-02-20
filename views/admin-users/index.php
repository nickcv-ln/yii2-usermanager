<?php
use yii\grid\GridView;
use yii\widgets\Pjax;
use nickcv\usermanager\enums\UserStatus;
use app\enums\ExtendedRoles;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $model nickcv\usermanager\models\UserSearch */

$this->title = 'Users | Admin Panel | '.\Yii::$app->name;
$this->params['breadcrumbs'][] = 'Admin Panel';
$this->params['breadcrumbs'][] = 'Users';

?>

<div class="jumbotron usermanager">
    <h1>Users</h1>
    <p>From here you can manage the existing users and create new ones.</p>
</div>

<?php Pjax::begin(); ?>
<?php echo GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $model,
    'emptyText' => 'No user found.',
    'columns' => [
        'email',
        'firstname',
        'lastname',
        [
            'attribute' => 'status',
            'value' => function($model) {
                return UserStatus::getLabel($model->status);
            },
            'filter' => UserStatus::getLabels(),
        ],
        [
            'attribute' => 'role',
            'value' => function($model) {
                return '<kbd>' . $model->role . '</kbd>';
            },
            'format' => 'html',
            'filter' => array_combine(ExtendedRoles::getList(), ExtendedRoles::getList()),
        ],
        [
            'class' => yii\grid\ActionColumn::className(),
        ]
    ],
]); ?>
<?php Pjax::end(); ?>