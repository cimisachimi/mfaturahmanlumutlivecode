<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Daftar Author Baru';
?>
<div class="site-signup">
    <div class="row">
        <div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3">
            <div class="login-panel" style="background-color: #FFFFFF; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); padding: 40px 30px; margin-top: 60px; border-top: 5px solid #2FA084;">
                
                <div class="text-center" style="margin-bottom: 30px;">
                    <h2 style="color: #008000; font-weight: 700; margin-top: 0;">Daftar Author</h2>
                    <p style="color: #666; font-size: 14px;">Bergabunglah menjadi penulis di LumutBlog.</p>
                </div>

                <?php $form = ActiveForm::begin(['id' => 'form-signup']); ?>

                    <!-- Username -->
                    <?= $form->field($model, 'username', [
                        'labelOptions' => ['style' => 'color: #008000; font-weight: 600;']
                    ])->textInput([
                        'autofocus' => true, 
                        'placeholder' => 'Pilih username unik...',
                        'style' => 'border-radius: 5px; height: 45px; border: 1px solid #ccc;'
                    ]) ?>

                    PHP

                    <!-- Password -->
                    <?= $form->field($model, 'password', [
                        'labelOptions' => ['style' => 'color: #008000; font-weight: 600;']
                    ])->passwordInput([
                        'placeholder' => 'Buat password...',
                        'style' => 'border-radius: 5px; height: 45px; border: 1px solid #ccc;'
                    ]) ?>
                    
                    <!-- Kolom Nama Lengkap Kini Aktif -->
                    <?= $form->field($model, 'name', [
                        'labelOptions' => ['style' => 'color: #008000; font-weight: 600;']
                    ])->textInput([
                        'placeholder' => 'Nama lengkap Anda...',
                        'style' => 'border-radius: 5px; height: 45px; border: 1px solid #ccc;'
                    ])->label('Nama Lengkap') ?>

                    <div class="form-group" style="margin-top: 30px;">
                        <?= Html::submitButton('Daftar Sekarang', [
                            'class' => 'btn btn-block', 
                            'style' => 'background-color: #2FA084; color: #FFFFFF; border: none; padding: 12px; font-size: 16px; font-weight: bold; border-radius: 5px;'
                        ]) ?>
                    </div>

                <?php ActiveForm::end(); ?>
                
                <div class="text-center" style="margin-top: 25px; padding-top: 20px; border-top: 1px solid #eaeaea;">
                    <span style="color: #666;">Sudah punya akun?</span><br>
                    <?= Html::a('Login di sini', ['site/login'], ['style' => 'color: #2FA084; font-weight: bold; display: inline-block; margin-top: 8px;']) ?>
                </div>

            </div>
        </div>
    </div>
</div>