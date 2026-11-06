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
        <?= $form->field($model, 'imageFile')->fileInput()->label('Upload Thumbnail (Opsional)') ?>
        <?php if ($model->thumbnail): ?>
            <p>Thumbnail Saat Ini:</p>
            <img src="<?= Yii::getAlias('@web/uploads/') . $model->thumbnail ?>" width="150" style="border-radius: 5px; margin-bottom: 10px;">
        <?php endif; ?>
    </div>

    <?= $form->field($model, 'status')->dropDownList(
        \app\models\Post::getStatusList(), 
        ['prompt' => 'Pilih Status Artikel...']
    ) ?>

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