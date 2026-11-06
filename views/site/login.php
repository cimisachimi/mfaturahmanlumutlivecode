<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap\ActiveForm $form */
/** @var app\models\LoginForm $model */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Welcome Back';
?>
<div class="site-login">
    <div class="row">
        <!-- Membuat form berada tepat di tengah layar -->
        <div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3">
            
            <!-- Card Container -->
            <div class="login-panel" style="background-color: #FFFFFF; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); padding: 40px 30px; margin-top: 60px; border-top: 5px solid #2FA084;">
                
                <div class="text-center" style="margin-bottom: 30px;">
                    <h2 style="color: #008000; font-weight: 700; margin-top: 0;"><?= Html::encode($this->title) ?></h2>
                    <p style="color: #666; font-size: 14px;">Silakan masukkan username dan password Anda.</p>
                </div>

                <?php $form = ActiveForm::begin([
                    'id' => 'login-form',
                    'options' => ['class' => 'form-vertical'],
                ]); ?>

                    <!-- Username Field -->
                    <?= $form->field($model, 'username', [
                        'labelOptions' => ['style' => 'color: #008000; font-weight: 600;']
                    ])->textInput([
                        'autofocus' => true, 
                        'placeholder' => 'Masukkan username...',
                        'style' => 'border-radius: 5px; padding: 10px 15px; height: 45px; border: 1px solid #ccc;'
                    ]) ?>

                    <!-- Password Field -->
                    <?= $form->field($model, 'password', [
                        'labelOptions' => ['style' => 'color: #008000; font-weight: 600;']
                    ])->passwordInput([
                        'placeholder' => 'Masukkan password...',
                        'style' => 'border-radius: 5px; padding: 10px 15px; height: 45px; border: 1px solid #ccc;'
                    ]) ?>

                    <!-- Remember Me Checkbox -->
                    <?= $form->field($model, 'rememberMe')->checkbox([
                        'template' => "<div class=\"checkbox\" style=\"color: #008000;\">\n{beginLabel}\n{input}\n{labelTitle}\n{endLabel}\n</div>\n<div class=\"col-lg-12\" style=\"padding:0;\">{error}</div>",
                    ])->label('Ingat Saya') ?>

                    <!-- Submit Button -->
                    <div class="form-group" style="margin-top: 30px;">
                        <?= Html::submitButton('Login', [
                            'class' => 'btn btn-block', 
                            'name' => 'login-button', 
                            'style' => 'background-color: #2FA084; color: #FFFFFF; border: none; padding: 12px; font-size: 16px; font-weight: bold; border-radius: 5px; transition: all 0.3s ease;'
                        ]) ?>
                    </div>

                <?php ActiveForm::end(); ?>
                
                <!-- BAGIAN REGISTRASI AUTHOR -->
                <div class="text-center" style="margin-top: 25px; padding-top: 20px; border-top: 1px solid #eaeaea;">
                    <span style="color: #666; font-size: 14px;">Belum punya akun?</span><br>
                    <?= Html::a('Daftar sebagai Author', ['site/signup'], [
                        'style' => 'color: #2FA084; font-weight: bold; text-decoration: none; display: inline-block; margin-top: 8px; font-size: 15px;'
                    ]) ?>
                </div>

            </div>
        </div>
    </div>
</div>