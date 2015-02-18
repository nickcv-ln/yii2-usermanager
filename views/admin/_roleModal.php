<?php
use yii\bootstrap\Modal;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model nickcv\usermanager\forms\RoleForm */

Modal::begin([
    'id' => 'role-modal',
    'header' => '<h2>Add Role</h2>',
    'size' => Modal::SIZE_LARGE,
    'toggleButton' => ['label' => 'add role', 'class' => 'btn btn-primary'],
]); ?>

<?php $form = ActiveForm::begin([
    'id' => 'new-role-form',
    'options' => ['autocomplete' => 'off'],
    'fieldConfig' => [
        'template' => "{label}\n<div class=\"\">{input}</div>\n<div class=\"\">{error}</div>",
        'labelOptions' => ['class' => 'control-label'],
    ],
    'action' => ['admin/roles/add-new-role'],
]);
?>

<div class="col-md-12">
    <?php echo $form->field($model, 'name')->textInput();  ?>
</div>
<div class="col-md-12">
    <?php echo $form->field($model, 'description')->textInput();  ?>
</div>
<div class="clearfix"></div>

<div class="col-md-12">
    <div class="">
        <?php echo Html::submitButton('create new role', ['class' => 'btn btn-primary', 'name' => 'new-role-button']) ?>
    </div>
</div>

<?php ActiveForm::end(); ?>

<div class="clearfix"></div>
<?php
Modal::end();
