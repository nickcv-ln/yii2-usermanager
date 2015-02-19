<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use nickcv\usermanager\enums\Registration;
use nickcv\usermanager\enums\GeneralSettings;
use nickcv\usermanager\enums\PasswordStrength;
use nickcv\usermanager\Module;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model nickcv\usermanager\forms\ConfigurationForm */

$this->title = 'Configuration | Admin Panel | '.\Yii::$app->name;
$this->params['breadcrumbs'][] = 'Admin Panel';
$this->params['breadcrumbs'][] = 'Configuration';
?>

<div class="jumbotron usermanager">
    <h1>Module Configuration</h1>
    <p>The following configuration is the one contained in the <kbd><?php echo Module::CONFIG_FILENAME; ?></kbd> file.</p>
    <p>You can either edit it from here or you can directly change the content of the file.</p>
</div>

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

<div class="form-group text-center">
    <?php echo Html::submitButton('Update Configuration', ['class' => 'btn btn-lg btn-primary', 'name' => 'configuration-button']) ?>
</div>

<?php ActiveForm::end(); ?>
