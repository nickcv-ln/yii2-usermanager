<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use nickcv\usermanager\enums\Scenarios;
use nickcv\usermanager\helpers\AuthHelper;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $permission yii\rbac\Permission */
/* @var $model nickcv\usermanager\forms\RoleForm */

$model->scenario = Scenarios::ROLE_DELETE;
?>

<?php if (!AuthHelper::isChildRoleProtected($model->parentRole, $model->name)): ?>

<?php $form = ActiveForm::begin([
    'id' => 'revoke-role-form-' . $model->name,
    'options' => ['autocomplete' => 'off'],
    'fieldConfig' => [
        'template' => "{label}\n<div class=\"\">{input}</div>\n<div class=\"\">{error}</div>",
        'labelOptions' => ['class' => 'control-label'],
    ],
    'action' => ['admin/roles/revoke-role'],
    'method' => 'DELETE',
]); ?>

<?php echo $form->field($model, 'parentRole', ['template' => '{input}'])->hiddenInput(); ?>
<?php echo $form->field($model, 'name', ['template' => '{input}'])->hiddenInput(); ?>

<?php echo Html::submitButton('revoke child role <span class="glyphicon glyphicon-trash"></span>', [
    'class' => 'btn btn-dark',
    'name' => 'revoke-permission-button',
    'data-confirm' => 'Are you sure you want to revoke the child role "' . $model->name . '"?'
]); ?>

<?php ActiveForm::end(); ?>
<?php endif;
