<?php
use yii\bootstrap\Modal;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;
use yii\helpers\Html;
use nickcv\usermanager\enums\Roles;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model nickcv\usermanager\forms\RoleForm */

Modal::begin([
    'id' => 'new-role-modal',
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
    <?php echo Alert::widget([
        'options' => [
            'class' => 'alert-info',
        ],
        'closeButton' => false,
        'body' => 'Every new role will automatically become a child role of both the <kbd>' . Roles::SUPER_ADMIN . '</kbd> and <kbd>' . Roles::ADMIN . '</kbd> roles.',
    ]); ?>
</div>

<div class="col-md-12">
    <?php echo $form->field($model, 'name')->textInput();  ?>
</div>
<div class="col-md-12">
    <?php echo $form->field($model, 'description')->textInput();  ?>
</div>
<div class="clearfix"></div>

<div class="col-md-12 text-center">
    <?php echo Html::submitButton('create new role', ['class' => 'btn btn-lg btn-primary', 'name' => 'new-role-button']) ?>
</div>

<?php ActiveForm::end(); ?>

<div class="clearfix"></div>
<?php
Modal::end();
