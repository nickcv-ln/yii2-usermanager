<?php
use yii\bootstrap\Alert;

/* @var $this \yii\web\View */

if (\Yii::$app->session->hasFlash('success')):
    echo Alert::widget([
        'options' => [
            'class' => 'alert-success',
        ],
        'body' => \Yii::$app->session->getFlash('success', null, true),
    ]);
endif;
if (\Yii::$app->session->hasFlash('error')):
    $error = \Yii::$app->session->getFlash('error', null, true);
    echo Alert::widget([
        'options' => [
            'class' => 'alert-danger',
        ],
        'body' => '<strong>' . $error['message'] . '</strong><br><br> - ' . implode('<br> - ', $error['errors']),
    ]);
endif;