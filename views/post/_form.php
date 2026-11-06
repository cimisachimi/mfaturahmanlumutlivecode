<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use dosamigos\ckeditor\CKEditor;

/* @var $this yii\web\View */
/* @var $model app\models\Post */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="post-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput() ?>

    <?= $form->field($model, 'content')->textarea(['rows' => 6])->widget(CKEditor::className(), [
                'preset' => 'full',
            ]) ?>


    <?= $form->field($model, 'status')->dropDownList(
    \app\models\Post::getStatusList(), 
    ['prompt' => 'Pilih Status Artikel...']
    ) ?>
    
    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
