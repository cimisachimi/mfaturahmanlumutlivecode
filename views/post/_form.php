<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

// Load Summernote CSS & JS via CDN
$this->registerCssFile("https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css");
$this->registerJsFile("https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js", ['depends' => [\yii\web\JqueryAsset::className()]]);
?>

<div class="post-form">
    <!-- Tambahkan enctype multipart/form-data untuk upload file -->
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?= $form->field($model, 'title')->textInput() ?>
    
    <!-- Input Upload Thumbnail -->
    <div style="margin-bottom: 20px; padding: 15px; border: 1px dashed #2FA084; border-radius: 5px;">
        <?= $form->field($model, 'imageFile')->fileInput(['accept' => 'image/jpeg, image/png, image/webp'])->label('Upload Thumbnail (Opsional)') ?>
        <?php if ($model->thumbnail): ?>
            <p>Thumbnail Saat Ini:</p>
            <img src="<?= Yii::getAlias('@web/uploads/') . $model->thumbnail ?>" width="150" style="border-radius: 5px; margin-bottom: 10px;">
        <?php endif; ?>
    </div>

    <?php
$username = Yii::$app->user->identity->username;
$isAdmin = (isset(Yii::$app->user->identity->role) ? strtolower(Yii::$app->user->identity->role) : strtolower($username)) === 'admin';
?>

<?php if ($isAdmin): ?>
    <!-- Tampilan Khusus Admin: Bisa ubah ke Approved/Reject dan Isi Catatan -->
    <div style="background: #f4fcf6; padding: 15px; border-radius: 5px; border: 1px solid #2FA084; margin-bottom: 20px;">
        <h4 style="color: #008000; margin-top: 0; font-weight: bold;">Panel Kontrol Admin</h4>
        
        <?= $form->field($model, 'status')->dropDownList(
            \app\models\Post::getStatusListForAdmin()
        ) ?>

        <?= $form->field($model, 'admin_note')->textarea([
            'rows' => 3, 
            'placeholder' => 'Tuliskan alasan penolakan atau catatan revisi untuk author di sini...'
        ])->label('Catatan / Alasan (Opsional untuk Reject/Approved)') ?>
    </div>
    <?php else: ?>
        <!-- Tampilan Khusus Author: Hanya bisa Draft atau Pending -->
        <?= $form->field($model, 'status')->dropDownList(
            \app\models\Post::getStatusListForAuthor()
        ) ?>

        <!-- Jika artikel pernah direject atau diberi catatan oleh admin, tampilkan agar dibaca author -->
        <?php if (!empty($model->admin_note)): ?>
            <div class="alert alert-warning" style="margin-top: 15px; border-left: 4px solid #f0ad4e;">
                <strong>Catatan / Alasan dari Admin:</strong><br>
                <p style="margin-top: 5px; color: #333;"><?= nl2br(Html::encode($model->admin_note)) ?></p>
            </div>
        <?php endif; ?>
    <?php endif; ?>
    <!-- Field Konten menggunakan ID summernote -->
    <?= $form->field($model, 'content')->textarea(['id' => 'summernote-editor']) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success', 'style' => 'background-color: #2FA084; border:none;']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<!-- Inisialisasi Script Editor -->
<?php
$script = <<< JS
$(document).ready(function() {
    $('#summernote-editor').summernote({
        height: 400,
        placeholder: 'Tulis isi artikel di sini... (Anda bisa drag & drop gambar langsung ke sini)',
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'clear']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['insert', ['link', 'picture', 'video']], // <-- Tombol untuk Upload Gambar di dalam Teks
            ['view', ['fullscreen', 'codeview']]
        ]
    });
});
JS;
$this->registerJs($script);
?>