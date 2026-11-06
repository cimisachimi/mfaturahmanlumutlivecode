<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Post */

$this->title = $model->title;

// Deteksi status login
$isGuest = Yii::$app->user->isGuest;
$currentUsername = !$isGuest ? Yii::$app->user->identity->username : '';
$isAdmin = (!Yii::$app->user->isGuest) && ((isset(Yii::$app->user->identity->role) ? strtolower(Yii::$app->user->identity->role) : strtolower($currentUsername)) === 'admin' || strpos(strtolower($currentUsername), 'adm') === 0);

// Atur breadcrumbs secara bersih (Tanpa memasukkan judul artikel ke dalam array breadcrumbs)
if ($isGuest) {
    // Kosongkan array breadcrumbs agar widget default layout Yii2 tidak mencetak "Beranda" ganda
    $this->params['breadcrumbs'] = [];
} else {
    $this->params['breadcrumbs'][] = ['label' => 'Manajemen Artikel', 'url' => ['index']];
}
?>
<div class="post-view" style="max-width: 800px; margin: 20px auto; background: #ffffff; padding: 40px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.04);">

    <!-- Header Artikel -->
    <div class="article-header" style="margin-bottom: 30px; border-bottom: 1px solid #eaeaea; padding-bottom: 25px;">
        
        <!-- KOTAK SISTEM TRACKING REVIEW (Hanya tampil untuk Admin atau Penulis Asli yang sudah login) -->
        <?php if (!$isGuest && ($isAdmin || $model->username === $currentUsername)): ?>
            <div class="panel panel-default" style="border: 1px solid #e0e0e0; border-radius: 8px; background-color: #fcfcfc; margin-bottom: 25px; box-shadow: none;">
                <div class="panel-body" style="padding: 15px 20px;">
                    <h4 style="color: #008000; font-weight: bold; margin-top: 0; margin-bottom: 15px; font-size: 15px;">
                        <span class="glyphicon glyphicon-time" style="margin-right: 5px;"></span> Status & Riwayat Review
                    </h4>
                    
                    <div class="row" style="font-size: 13px; color: #555;">
                        <div class="col-sm-4" style="margin-bottom: 8px;">
                            <strong>🕒 Disubmit Pada:</strong><br>
                            <span style="color: #333;"><?= !empty($model->submitted_at) ? date('d M Y, H:i', strtotime($model->submitted_at)) : '<span class="text-muted">Belum disubmit</span>' ?></span>
                        </div>
                        
                        <div class="col-sm-4" style="margin-bottom: 8px;">
                            <strong>👁️ Direview Pada:</strong><br>
                            <span style="color: #333;"><?= !empty($model->reviewed_at) ? date('d M Y, H:i', strtotime($model->reviewed_at)) : '<span class="text-muted">Menunggu review</span>' ?></span>
                        </div>

                        <div class="col-sm-4" style="margin-bottom: 8px;">
                            <strong>🔄 Siklus Revisi:</strong><br>
                            <span class="label" style="background-color: #2FA084; font-size: 12px; padding: 3px 8px;">Revisi ke-<?= (int)$model->revision_count ?></span>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Status Badge (Hanya tampil jika user sudah login / BUKAN guest) -->
        <?php if (!$isGuest): ?>
            <div style="margin-bottom: 15px;">
                <?php 
                    $status = strtolower($model->status);
                    if($status === 'approved') {
                        echo '<span style="background-color: #2FA084; color: white; padding: 4px 12px; border-radius: 12px; font-size: 11px; font-weight: bold; text-transform: uppercase;">Approved</span>';
                    } elseif($status === 'pending') {
                        echo '<span style="background-color: #f0ad4e; color: white; padding: 4px 12px; border-radius: 12px; font-size: 11px; font-weight: bold; text-transform: uppercase;">Pending Review</span>';
                    } elseif($status === 'reject') {
                        echo '<span style="background-color: #d9534f; color: white; padding: 4px 12px; border-radius: 12px; font-size: 11px; font-weight: bold; text-transform: uppercase;">Rejected</span>';
                    } else {
                        echo '<span style="background-color: #777; color: white; padding: 4px 12px; border-radius: 12px; font-size: 11px; font-weight: bold; text-transform: uppercase;">Draft</span>';
                    }
                ?>
            </div>
        <?php endif; ?>

        <h1 style="color: #008000; font-weight: 800; font-size: 32px; line-height: 1.3; margin-top: 0; margin-bottom: 15px;">
            <?= Html::encode($model->title) ?>
        </h1>

        <div style="color: #666; font-size: 14px; display: flex; align-items: center; gap: 15px;">
            <span><span class="glyphicon glyphicon-user" style="color: #2FA084; margin-right: 5px;"></span> <strong><?= Html::encode($model->username) ?></strong></span>
            <span>&bull;</span>
            <span><span class="glyphicon glyphicon-calendar" style="color: #2FA084; margin-right: 5px;"></span> <?= date('d M Y, H:i', strtotime($model->date)) ?></span>
        </div>
    </div>

    <!-- Catatan Penolakan (Jika ada dan dilihat author) -->
    <?php if (!empty($model->admin_note)): ?>
        <div class="alert alert-danger" style="border-left: 5px solid #d9534f; border-radius: 4px; margin-bottom: 30px;">
            <strong>Catatan / Alasan Penolakan dari Admin:</strong><br>
            <p style="margin-top: 5px; margin-bottom: 0;"><?= nl2br(Html::encode($model->admin_note)) ?></p>
        </div>
    <?php endif; ?>

    <!-- Thumbnail Utama Artikel -->
    <?php if (!empty($model->thumbnail)): ?>
        <div class="article-thumbnail" style="margin-bottom: 35px; text-align: center;">
            <img src="<?= Yii::$app->request->baseUrl . '/uploads/' . Html::encode($model->thumbnail) ?>" alt="Thumbnail" style="max-width: 100%; max-height: 450px; border-radius: 8px; object-fit: cover; box-shadow: 0 4px 12px rgba(0,0,0,0.08);">
        </div>
    <?php endif; ?>

    <!-- Konten Artikel (Mendukung HTML dari Summernote) -->
    <div class="article-content" style="font-size: 16px; line-height: 1.8; color: #2c3e50; word-break: break-word;">
        <?= $model->content ?>
    </div>

    <!-- Tombol Navigasi Bawah -->
    <div class="article-footer" style="margin-top: 50px; padding-top: 20px; border-top: 1px solid #eaeaea; display: flex; justify-content: space-between; align-items: center;">
        <?= Html::a('<span class="glyphicon glyphicon-arrow-left" style="margin-right: 5px;"></span> Kembali', $isGuest ? ['site/index'] : ['index'], [
            'class' => 'btn btn-default',
            'style' => 'border-radius: 20px; padding: 8px 20px; font-weight: 600;'
        ]) ?>
        
        <?php if (!$isGuest && ($isAdmin || $model->username === $currentUsername)): ?>
            <div>
                <?= Html::a('<span class="glyphicon glyphicon-pencil" style="margin-right: 5px;"></span> Edit Artikel', ['update', 'id' => $model->idpost], [
                    'class' => 'btn btn-primary',
                    'style' => 'background-color: #2FA084; border-color: #2FA084; border-radius: 20px; padding: 8px 20px; font-weight: 600;'
                ]) ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- KOTAK FORMULIR REVIEW KHUSUS ADMIN -->
    <?php if ($isAdmin && strtolower($model->status) === 'pending'): ?>
        <div class="panel panel-primary" style="border-color: #2FA084; margin-top: 40px; border-radius: 8px; box-shadow: 0 4px 15px rgba(47,160,132,0.1);">
            <div class="panel-heading" style="background-color: #2FA084; border-color: #2FA084; border-top-left-radius: 7px; border-top-right-radius: 7px; padding: 12px 20px;">
                <h3 class="panel-title" style="font-weight: bold; color: white; font-size: 16px;">
                    <span class="glyphicon glyphicon-check" style="margin-right: 5px;"></span> Panel Keputusan Review Admin
                </h3>
            </div>
            <div class="panel-body" style="padding: 20px; background: #f9fbfa;">
                
                <form action="<?= \yii\helpers\Url::to(['view', 'id' => $model->idpost]) ?>" method="post">
                    <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->getCsrfToken() ?>">
                    
                    <div class="form-group">
                        <label style="color: #008000; font-weight: bold;">Catatan / Alasan Penolakan (Wajib diisi jika ingin melakukan Revisi/Reject):</label>
                        <textarea name="admin_note" class="form-control" rows="3" placeholder="Tuliskan bagian mana yang perlu diperbaiki oleh author..."><?= Html::encode($model->admin_note) ?></textarea>
                    </div>

                    <div style="display: flex; gap: 10px; margin-top: 15px;">
                        <button type="submit" name="action" value="approve" class="btn btn-success btn-lg" style="background-color: #2FA084; border-color: #2FA084; font-weight: bold; flex: 1;" onclick="return confirm('Yakin ingin menyetujui artikel ini?')">
                            <span class="glyphicon glyphicon-ok" style="margin-right: 5px;"></span> Approve (Setujui)
                        </button>
                        
                        <button type="submit" name="action" value="reject" class="btn btn-danger btn-lg" style="font-weight: bold; flex: 1;" onclick="return confirm('Yakin ingin menolak/meminta revisi artikel ini?')">
                            <span class="glyphicon glyphicon-remove" style="margin-right: 5px;"></span> Revisi / Reject
                        </button>
                    </div>
                </form>

            </div>
        </div>
    <?php endif; ?>

    <!-- BAGIAN SISTEM KOMENTAR INTERAKTIF -->
    <div class="comments-section" style="margin-top: 50px; padding-top: 30px; border-top: 2px solid #eaeaea;">
        <h3 style="color: #008000; font-weight: 800; margin-bottom: 25px;">
            <span class="glyphicon glyphicon-comment" style="margin-right: 8px; color: #2FA084;"></span> 
            Diskusi & Komentar (<?= count($model->comments) ?>)
        </h3>

        <!-- Form Tambah Komentar -->
        <?php if ($isGuest): ?>
            <div class="alert alert-info" style="border-radius: 8px;">
                Silakan <?= Html::a('Login', ['site/login'], ['style' => 'font-weight: bold; color: #008000;']) ?> terlebih dahulu untuk ikut berdiskusi dan menulis komentar.
            </div>
        <?php else: ?>
            <div class="panel panel-default" style="border: 1px solid #e0e0e0; border-radius: 8px; box-shadow: none; margin-bottom: 30px;">
                <div class="panel-body" style="padding: 20px;">
                    <?php $form = \yii\widgets\ActiveForm::begin([
                        'action' => ['view', 'id' => $model->idpost],
                        'options' => ['style' => 'margin: 0;']
                    ]); ?>

                    <?= $form->field(new \app\models\Comment(), 'content')->textarea([
                        'rows' => 3, 
                        'placeholder' => 'Tulis tanggapan atau pertanyaan Anda di sini...',
                        'style' => 'border-radius: 6px; resize: vertical;'
                    ])->label('Tinggalkan Komentar sebagai <strong>' . Html::encode($currentUsername) . '</strong>') ?>

                    <div class="form-group" style="margin-bottom: 0; text-align: right;">
                        <?= Html::submitButton('<span class="glyphicon glyphicon-send" style="margin-right: 5px;"></span> Kirim Komentar', [
                            'class' => 'btn btn-success',
                            'style' => 'background-color: #2FA084; border-color: #2FA084; font-weight: bold; border-radius: 20px; padding: 8px 20px;'
                        ]) ?>
                    </div>

                    <?php \yii\widgets\ActiveForm::end(); ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- List Daftar Komentar -->
        <div class="comments-list">
            <?php 
            $comments = \app\models\Comment::find()->where(['post_id' => $model->idpost])->orderBy(['created_at' => SORT_DESC])->all();
            
            if (empty($comments)): 
            ?>
                <p style="color: #777; font-style: italic;">Belum ada komentar pada artikel ini. Jadilah yang pertama memberikan tanggapan!</p>
            <?php else: ?>
                <?php foreach ($comments as $comm): ?>
                    <div style="background: #fbfdfc; border: 1px solid #eaeaea; border-radius: 8px; padding: 15px 20px; margin-bottom: 15px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                            <div>
                                <span style="font-weight: bold; color: #008000; font-size: 15px;">
                                    <span class="glyphicon glyphicon-user" style="color: #2FA084; margin-right: 5px;"></span> 
                                    <?= Html::encode($comm->username) ?>
                                </span>
                                <span style="color: #999; font-size: 12px; margin-left: 10px;">
                                    <?= date('d M Y, H:i', strtotime($comm->created_at)) ?>
                                </span>
                            </div>

                            <!-- Tombol Hapus Komentar (Moderasi) -->
                            <?php if (!$isGuest && ($isAdmin || $model->username === $currentUsername || $comm->username === $currentUsername)): ?>
                                <?= Html::a('<span class="glyphicon glyphicon-trash"></span> Hapus', ['delete-comment', 'id' => $comm->id], [
                                    'class' => 'btn btn-xs btn-default',
                                    'style' => 'color: #d9534f; border-color: #ddd;',
                                    'data-confirm' => 'Apakah Anda yakin ingin menghapus komentar ini?',
                                    'data-method' => 'post',
                                    'title' => 'Hapus Komentar'
                                ]) ?>
                            <?php endif; ?>
                            <!-- Tombol Report Komentar (Muncul untuk user yang login dan bukan pemilik komentar) -->
                            <?php if (!is_null(Yii::$app->user->identity) && $comm->username !== $currentUsername && !$isAdmin): ?>
                                <?= Html::a('<span class="glyphicon glyphicon-flag" style="color: #f0ad4e;"></span> Laporkan', ['report-comment', 'id' => $comm->id], [
                                    'class' => 'btn btn-xs btn-default',
                                    'style' => 'border-color: #ddd; margin-left: 5px;',
                                    'data-confirm' => 'Yakin ingin melaporkan komentar ini kepada Admin?',
                                    'data-method' => 'post',
                                    'title' => 'Laporkan komentar tidak pantas'
                                ]) ?>
                            <?php endif; ?>
                        </div>

                        <p style="color: #333; font-size: 14px; line-height: 1.6; margin: 0; word-break: break-word;">
                            <?= nl2br(Html::encode($comm->content)) ?>
                        </p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

</div>