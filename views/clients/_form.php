<?php

use rmrevin\yii\fontawesome\component\Icon;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model promocat\oauth2\models\Client */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="client-form">
    <div class="row">
        <div class="col-lg-6">
            <?php $form = ActiveForm::begin(); ?>

            <?php \promocat\adminlte\widgets\Box::begin(
                [
                    "type" => \promocat\adminlte\widgets\Box::TYPE_INFO,
                    "header" => $this->title,
                    "icon" => "pencil",
                    "footer" => join('', [
                        Html::a('Cancel', 'javascript:void(0)', ['onclick' => 'window.history.back()', 'class' => 'btn btn-default']),
                        Html::submitButton('Save', ['class' => 'btn btn-primary pull-right', 'name' => 'signup-button'])
                    ]),
                ]
            )
            ?>
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'identifier')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'redirect_uri')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'secret',['options'=>['class'=>'has-warning form-group']])->label('<span style="color:#000">'.$model->getAttributeLabel('secret').'</span> '.(new Icon('bell')).' '.Yii::t('oauth2','Store this secret securely, it will not be shown again!'))->textInput(['readonly'=>true,'maxlength' => true]) ?>

            <?php \promocat\adminlte\widgets\Box::end() ?>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>