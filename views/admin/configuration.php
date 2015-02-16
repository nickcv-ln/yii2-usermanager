<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use nickcv\usermanager\AssetBundle;
use nickcv\usermanager\enums\Registration;
use nickcv\usermanager\enums\GeneralSettings;
use nickcv\usermanager\enums\PasswordStrength;
use nickcv\usermanager\Module;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model nickcv\usermanager\forms\ConfigurationForm */

$this->title = 'Admin Panel | '.\Yii::$app->name;
$this->params['breadcrumbs'][] = 'Admin Panel';

echo $this->render('_heroUnit');
?>

<div class="col-lg-12">
    <h2>Module Configuration</h2>
    <p>The following configuration is the one contained in the <mark><?php echo Module::CONFIG_FILENAME; ?></mark> file.</p>
    <p>You can either edit it from here or you can directly change the content of the file.</p>
    <hr>
    <?php $form = ActiveForm::begin([
        'id' => 'configuration-form',
        'options' => ['autocomplete' => 'off'],
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"\">{input}</div>\n<div class=\"\">{error}</div>",
            'labelOptions' => ['class' => 'control-label'],
        ],
    ]); ?>
    
    <div class="col-md-4">
        <?php echo $form->field($model, 'registration')->radioList(Registration::getLabels()); ?>
    </div>
    <div class="col-md-4">
        <?php echo $form->field($model, 'activation')->radioList(GeneralSettings::getLabels()); ?>
    </div>
    <div class="col-md-4">
        <?php echo $form->field($model, 'passwordRecovery')->radioList(GeneralSettings::getLabels()); ?>
    </div>
    <div class="clearfix"></div>
    <div class="col-md-12">
        <?php echo $form->field($model, 'passwordStrength')->radioList(PasswordStrength::getLabels()); ?>
    </div>
    <div class="clearfix"></div>

    <div class="form-group">
        <div class="">
            <?= Html::submitButton('Update Configuration', ['class' => 'btn btn-primary', 'name' => 'configuration-button']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>
