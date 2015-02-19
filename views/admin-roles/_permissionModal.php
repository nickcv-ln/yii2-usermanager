<?php
use yii\bootstrap\Modal;
use nickcv\usermanager\helpers\AuthHelper;

/* @var $this yii\web\View */
/* @var $permissionForm nickcv\usermanager\forms\PermissionForm */

Modal::begin([
    'id' => 'permission-modal',
    'header' => '<h2>Add Permission</h2>',
    'size' => Modal::SIZE_LARGE,
    'toggleButton' => ['label' => 'add permission', 'class' => 'btn btn-primary'],
]); ?>

<?php if (AuthHelper::getMissingPermissions($permissionForm->role)): ?>
<div class="col-md-6">
    <?php echo $this->render('_addExistingPermissionForm', ['model' => $permissionForm]); ?>
</div>
<div class="col-md-6">
    <?php echo $this->render('_newPermissionForm', ['model' => $permissionForm]); ?>
</div>
<?php else: ?>
<div class="col-md-12">
    <?php echo $this->render('_newPermissionForm', ['model' => $permissionForm]); ?>
</div>
<?php endif; ?>
<div class="clearfix"></div>
<?php
Modal::end();
