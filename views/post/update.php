<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Post */

$this->title = 'Edit Artikel: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Manajemen Artikel', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->idpost]];
$this->params['breadcrumbs'][] = 'Edit';
?>
<div class="post-update" style="max-width: 850px; margin: 20px auto;">

    <div style="background: #ffffff; padding: 35px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.04);">
        
        <h2 style="color: #008000; font-weight: 800; margin-top: 0; margin-bottom: 20px;">
            <?= Html::encode($this->title) ?>
        </h2>

        <!-- KOTAK INFO TRACKING & RIWAYAT REVISI DI HALAMAN EDIT -->
        <div class="panel panel-default" style="border: 1px solid #e0e0e0; border-radius: 8px; background-color: #fcfcfc; margin-bottom: 25px; box-shadow: none;">
            <div class="panel-body" style="padding: 15px 20px;">
                <h4 style="color: #008000; font-weight: bold; margin-top: 0; margin-bottom: 12px; font-size: 14px;">
                    <span class="glyphicon glyphicon-time" style="margin-right: 5px;"></span> Status & Siklus Tracking
                </h4>
                
                <div class="row" style="font-size: 13px; color: #555;">
                    <div class="col-sm-4" style="margin-bottom: 6px;">
                        <strong>Disubmit:</strong> 
                        <span style="color: #333;"><?= !empty($model->submitted_at) ? date('d M Y, H:i', strtotime($model->submitted_at)) : 'Belum' ?></span>
                    </div>
                    <div class="col-sm-4" style="margin-bottom: 6px;">
                        <strong>Direview:</strong> 
                        <span style="color: #333;"><?= !empty($model->reviewed_at) ? date('d M Y, H:i', strtotime($model->reviewed_at)) : 'Belum' ?></span>
                    </div>
                    <div class="col-sm-4" style="margin-bottom: 6px;">
                        <strong>Siklus Revisi:</strong> 
                        <span class="label" style="background-color: #2FA084; font-size: 11px;">Revisi ke-<?= (int)$model->revision_count ?></span>
                    </div>
                </div>

                <!-- Tampilkan catatan penolakan langsung di form edit jika status reject -->
                <?php if (!empty($model->admin_note)): ?>
                    <div style="margin-top: 12px; padding: 10px; background-color: #fdf7f7; border-left: 3px solid #d9534f; border-radius: 4px; color: #d9534f; font-size: 13px;">
                        <strong>Catatan Admin / Alasan Ditolak:</strong><br>
                        <?= nl2br(Html::encode($model->admin_note)) ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Render Form Utama -->
        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>

    </div>

</div>