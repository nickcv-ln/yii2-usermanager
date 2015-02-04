<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use nickcv\usermanager\AssetBundle;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model nickcv\usermanager\forms\LoginForm */

AssetBundle::register($this);

$this->title = 'Login | '.\Yii::$app->name;
$this->params['breadcrumbs'][] = $this->title;
?>
<div id="usermanager-login">
    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
        'options' => ['autocomplete' => 'off'],
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"\">{input}</div>\n<div class=\"\">{error}</div>",
            'labelOptions' => ['class' => 'control-label'],
        ],
    ]); ?>

    <?= $form->field($model, 'email') ?>

    <?= $form->field($model, 'password')->passwordInput() ?>

    <?= $form->field($model, 'rememberMe', [
        'template' => "<div class=\"\">{input}</div>\n<div class=\"\">{error}</div>",
    ])->checkbox() ?>

    <div class="form-group">
        <div class="">
            <?= Html::submitButton('Login', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>
