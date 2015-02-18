<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use nickcv\usermanager\enums\Scenarios;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model nickcv\usermanager\forms\PermissionForm */

$model->scenario = Scenarios::PERMISSION_NEW;

$form = ActiveForm::begin([
    'id' => 'new-permission-form',
    'options' => ['autocomplete' => 'off'],
    'fieldConfig' => [
        'template' => "{label}\n<div class=\"\">{input}</div>\n<div class=\"\">{error}</div>",
        'labelOptions' => ['class' => 'control-label'],
    ],
    'action' => ['admin/roles/add-new-permission'],
]);
?>

<?php echo $form->field($model, 'role', ['template' => '{input}'])->hiddenInput(); ?>

<div class="col-md-12">
    <?php echo $form->field($model, 'name')->textInput();  ?>
</div>
<div class="col-md-12">
    <?php echo $form->field($model, 'description')->textInput();  ?>
</div>
<div class="clearfix"></div>

<div class="col-md-12">
    <?php echo Html::submitButton('create permission and add it to role', ['class' => 'btn btn-primary', 'name' => 'new-permission-button']) ?>
</div>

<?php ActiveForm::end();