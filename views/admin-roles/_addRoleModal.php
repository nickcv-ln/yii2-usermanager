<?php
use yii\bootstrap\Modal;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use nickcv\usermanager\helpers\AuthHelper;
use nickcv\usermanager\enums\Scenarios;

/* @var $this yii\web\View */
/* @var $model nickcv\usermanager\forms\RoleForm */

if (AuthHelper::getMissingRoles($model->parentRole)):

    $model->scenario = Scenarios::ROLE_ADD;

    Modal::begin([
        'id' => 'add-role-modal',
        'header' => '<h2>Add Child Role</h2>',
        'size' => Modal::SIZE_DEFAULT,
        'toggleButton' => ['label' => 'add child role', 'class' => 'btn btn-primary'],
    ]); ?>

    <?php foreach(AuthHelper::getMissingRoles($model->parentRole) as $role): ?>
        <?php $model->name = $role->name; ?>
        <?php $form = ActiveForm::begin([
            'id' => 'add-role-form-' . $model->name,
            'options' => ['autocomplete' => 'off'],
            'fieldConfig' => [
                'template' => "{label}\n<div class=\"\">{input}</div>\n<div class=\"\">{error}</div>",
                'labelOptions' => ['class' => 'control-label'],
            ],
            'action' => ['admin/roles/add-existing-role'],
            'method' => 'PUT',
        ]);
        ?>

        <?php echo $form->field($model, 'parentRole', ['template' => '{input}'])->hiddenInput(); ?>
        <?php echo $form->field($model, 'name', ['template' => '{input}'])->hiddenInput(); ?>


        <?php echo Html::submitButton($model->name, ['class' => 'btn btn-primary', 'name' => 'add-role-button']) ?>


        <?php ActiveForm::end(); ?>

    <?php endforeach; ?>

    <div class="clearfix"></div>
    <?php
    Modal::end();
    $model->name = null;
endif;
