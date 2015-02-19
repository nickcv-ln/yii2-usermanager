<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use nickcv\usermanager\enums\Scenarios;
use nickcv\usermanager\helpers\AuthHelper;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $permission yii\rbac\Permission */
/* @var $model nickcv\usermanager\forms\PermissionForm */

$model->scenario = Scenarios::PERMISSION_DELETE;
$model->name = $permission->name;
?>

<?php if (!AuthHelper::isRolePermissionProtected($model->role, $model->name)): ?>

<?php $form = ActiveForm::begin([
    'id' => 'revoke-permission-form-' . $model->name,
    'options' => ['autocomplete' => 'off'],
    'fieldConfig' => [
        'template' => "{label}\n<div class=\"\">{input}</div>\n<div class=\"\">{error}</div>",
        'labelOptions' => ['class' => 'control-label'],
    ],
    'action' => ['admin/roles/revoke-permission'],
    'method' => 'DELETE',
]); ?>

<?php echo $form->field($model, 'role', ['template' => '{input}'])->hiddenInput(); ?>
<?php echo $form->field($model, 'name', ['template' => '{input}'])->hiddenInput(); ?>

<?php echo Html::submitButton('<span class="glyphicon glyphicon-trash"></span>', [
    'class' => 'btn btn-primary',
    'name' => 'revoke-permission-button',
    'data-confirm' => 'Are you sure you want to revoke the permission "' . $model->name . '"?'
]); ?>

<?php ActiveForm::end(); ?>
<?php endif;

$model->name = null;