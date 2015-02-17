<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use nickcv\usermanager\helpers\AuthHelper;
use nickcv\usermanager\helpers\ArrayHelper;
use nickcv\usermanager\enums\Scenarios;

/* @var $this yii\web\View */
/* @var $role string */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model nickcv\usermanager\forms\PermissionForm */

$model->scenario = Scenarios::PERMISSION_ADD;
?>

<?php $form = ActiveForm::begin([
    'id' => 'existing-permission-form',
    'options' => ['autocomplete' => 'off'],
    'fieldConfig' => [
        'template' => "{label}\n<div class=\"\">{input}</div>\n<div class=\"\">{error}</div>",
        'labelOptions' => ['class' => 'control-label'],
    ],
    'action' => ['admin/roles/add-existing-permission'],
    'method' => 'PUT',
]);

?>

<?php echo $form->field($model, 'role', ['template' => '{input}'])->hiddenInput(); ?>

<?php echo $form->field($model, 'existingPermissions')->checkboxList(ArrayHelper::map(AuthHelper::getMissingPermissions($model->role), 'name', 'description'));  ?>

<div class="form-group">
    <div class="">
        <?php echo Html::submitButton('add permissions to role', ['class' => 'btn btn-primary', 'name' => 'existing-permission-button']) ?>
    </div>
</div>

<?php ActiveForm::end();
