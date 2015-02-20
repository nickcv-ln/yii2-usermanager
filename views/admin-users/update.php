<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use nickcv\usermanager\helpers\AuthHelper;

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
        'template' => "{label}\n<div class=\"\">{input}</div>\n<div class=\"\">{hint}{error}</div>",
        'labelOptions' => ['class' => 'control-label'],
    ],
]);

echo $form->errorSummary($model);
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
    <?php echo $form->field($model, 'newPassword')->passwordInput()->hint('Leave this field blank if you don\'t want to update the password.');  ?>
</div>
<div class="col-md-6">
    <?php echo $form->field($model, 'role')->dropDownList(ArrayHelper::map(AuthHelper::getAllRolesExcludingParentRoles(AuthHelper::getUserRoleName(\Yii::$app->user->id)), 'name', 'name')); ?>
</div>
<div class="clearfix"></div>

<div class="col-md-12 text-center">
    <?php echo Html::submitButton('update user data', ['class' => 'btn btn-lg btn-primary', 'name' => 'edit-user-button']) ?>
</div>

<?php ActiveForm::end(); ?>