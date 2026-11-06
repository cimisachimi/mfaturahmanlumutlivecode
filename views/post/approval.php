<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Request Approval Artikel';
$this->params['breadcrumbs'][] = $this->title;

// Fungsi helper kecil untuk menghitung durasi waktu berlalu (Time Ago)
function timeAgo($datetime) {
    if (empty($datetime)) return '-';
    $time = strtotime($datetime);
    $now = time();
    $diff = $now - $time;

    if ($diff < 60) {
        return 'Baru saja';
    } elseif ($diff < 3600) {
        $mins = floor($diff / 60);
        return $mins . ' menit yang lalu';
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . ' jam yang lalu';
    } else {
        $days = floor($diff / 86400);
        return $days . ' hari yang lalu';
    }
}
?>
<div class="post-approval" style="max-width: 1000px; margin: 0 auto;">

    <div style="margin-bottom: 25px;">
        <h1 style="color: #008000; font-weight: 800; margin: 0;"><?= Html::encode($this->title) ?></h1>
        <p style="color: #666; margin-top: 5px;">Daftar artikel dari Author yang menunggu peninjauan, persetujuan, atau revisi.</p>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table', 'style' => 'background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05);'],
        'summary' => '<div style="margin-bottom: 15px; color: #666; font-size: 13px;">Menampilkan <b>{begin}-{end}</b> dari total <b>{totalCount}</b> request pending.</div>',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'title',
                'label' => 'Judul & Detail Penulis',
                'format' => 'raw',
                'value' => function($model) {
                    $titleLink = Html::a(Html::encode($model->title), ['view', 'id' => $model->idpost], [
                        'style' => 'font-weight: bold; color: #008000; font-size: 16px; text-decoration: none;'
                    ]);
                    $authorInfo = '<br><small style="color: #666;">Penulis: <strong style="color: #2FA084;">' . Html::encode($model->username) . '</strong> &bull; Siklus: Revisi ke-' . (int)$model->revision_count . '</small>';
                    
                    // Informasi Tanggal dan Durasi Waktu Sejak Disubmit
                    if (!empty($model->submitted_at)) {
                        $formattedDate = date('d M Y, H:i', strtotime($model->submitted_at));
                        $durationText = timeAgo($model->submitted_at);
                        $timeInfo = '<br><small style="color: #d9534f; font-weight: 500;">🕒 Disubmit: ' . $formattedDate . ' (' . $durationText . ')</small>';
                    } else {
                        $timeInfo = '<br><small style="color: #999;">🕒 Belum tercatat</small>';
                    }

                    return $titleLink . $authorInfo . $timeInfo;
                }
            ],
            [
                'label' => 'Aksi Review & Keputusan',
                'format' => 'raw',
                'contentOptions' => ['style' => 'width: 420px; vertical-align: middle; background: #fbfdfc;'],
                'value' => function($model) {
                    $formHtml = '<form action="' . \yii\helpers\Url::to(['post/process-approval', 'id' => $model->idpost]) . '" method="post" style="margin: 0;">';
                    $formHtml .= '<input type="hidden" name="' . Yii::$app->request->csrfParam . '" value="' . Yii::$app->request->getCsrfToken() . '">';
                    
                    $formHtml .= '<div style="margin-bottom: 8px;">';
                    $formHtml .= '<textarea name="admin_note" class="form-control input-sm" rows="2" placeholder="Tulis catatan/alasan jika ingin merevisi/menolak..."></textarea>';
                    $formHtml .= '</div>';

                    $formHtml .= '<div style="display: flex; gap: 8px;">';
                    $formHtml .= '<button type="submit" name="action" value="approve" class="btn btn-success btn-sm" style="background-color: #2FA084; border-color: #2FA084; font-weight: bold; flex: 1;" onclick="return confirm(\'Setujui (Approve) artikel ini?\')"><span class="glyphicon glyphicon-ok"></span> Approve</button>';
                    $formHtml .= '<button type="submit" name="action" value="reject" class="btn btn-danger btn-sm" style="font-weight: bold; flex: 1;" onclick="return confirm(\'Tolak/Minta revisi artikel ini?\')"><span class="glyphicon glyphicon-remove"></span> Revisi / Reject</button>';
                    $formHtml .= '</div>';
                    
                    $formHtml .= '</form>';
                    return $formHtml;
                }
            ],
        ],
    ]); ?>

</div>