<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use nickcv\usermanager\enums\UserStatus;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model nickcv\usermanager\models\User */

$this->title = 'Edit | ' . $model->email . ' | Users | Admin Panel | '.\Yii::$app->name;
$this->params['breadcrumbs'][] = 'Admin Panel';
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['admin/users']];
$this->params['breadcrumbs'][] = ['label' => $model->email, 'url' => ['admin/users/' . $model->id]];
$this->params['breadcrumbs'][] = 'Edit';

?>

<h1><?php echo $model->getFullName(); ?> [<?php echo $model->email; ?>]</h1>

<?php $form = ActiveForm::begin([
    'id' => 'edit-user-form',
    'options' => ['autocomplete' => 'off'],
    'fieldConfig' => [
        'template' => "{label}\n<div class=\"\">{input}</div>\n<div class=\"\">{error}</div>",
        'labelOptions' => ['class' => 'control-label'],
    ],
    'action' => ['admin/roles/add-new-role'],
]);
?>

<div class="col-md-6">
    <?php echo $form->field($model, 'firstname')->textInput();  ?>
</div>
<div class="col-md-6">
    <?php echo $form->field($model, 'lastname')->textInput();  ?>
</div>
<div class="col-md-6">
    <?php echo $form->field($model, 'email')->textInput();  ?>
</div>
<div class="col-md-6">
    <?php echo $form->field($model, 'password')->passwordInput();  ?>
</div>
<div class="clearfix"></div>

<div class="col-md-12">
    <?php echo Html::submitButton('update user data', ['class' => 'btn btn-primary', 'name' => 'edit-user-button']) ?>
</div>

<?php ActiveForm::end(); ?>