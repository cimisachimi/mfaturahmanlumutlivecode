<?php
use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'Panel Approval Admin';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="post-approval" style="max-width: 1200px; margin: 0 auto; background: #ffffff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.04);">

    <div style="margin-bottom: 25px;">
        <h1 style="color: #2FA084; font-weight: 800; margin: 0;">
            <span class="glyphicon glyphicon-check" style="margin-right: 10px;"></span> <?= Html::encode($this->title) ?>
        </h1>
        <p style="color: #666; margin-top: 5px;">Kelola artikel yang menunggu persetujuan dari Author.</p>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-hover table-bordered', 'style' => 'background: #fff; border-radius: 8px; overflow: hidden;'],
        'summary' => '<div style="margin-bottom: 15px; color: #666; font-size: 13px;">Menampilkan <b>{begin}-{end}</b> dari total <b>{totalCount}</b> artikel menunggu review.</div>',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'title',
                'label' => 'Judul Artikel',
                'format' => 'raw',
                'value' => function($model) {
                    return '<strong>' . Html::a(Html::encode($model->title), ['view', 'id' => $model->idpost], ['style' => 'color: #008000;']) . '</strong><br>' .
                           '<small style="color: #888;">Ditulis oleh <b>' . Html::encode($model->username) . '</b></small>';
                }
            ],
            [
                'attribute' => 'submitted_at',
                'label' => 'Waktu Submit',
                'value' => function($model) {
                    return !empty($model->submitted_at) ? date('d M Y, H:i', strtotime($model->submitted_at)) : '-';
                }
            ],
            [
                'label' => 'Aksi Cepat',
                'format' => 'raw',
                'contentOptions' => ['style' => 'width: 280px; text-align: center; vertical-align: middle;'],
                'value' => function($model) {
                    // MENGGUNAKAN data-confirm AGAR SWEETALERT MENYALA
                    $approveBtn = Html::a('<span class="glyphicon glyphicon-ok"></span> Approve', ['process-approval', 'id' => $model->idpost], [
                        'class' => 'btn btn-success btn-sm',
                        'style' => 'margin-right: 5px; font-weight: bold; background-color: #2FA084; border-color: #2FA084;',
                        'data' => [
                            'confirm' => 'Yakin ingin menyetujui artikel ini untuk dipublikasikan?',
                            'method' => 'post',
                            'params' => ['action' => 'approve']
                        ],
                    ]);
                    
                    $rejectBtn = Html::a('<span class="glyphicon glyphicon-remove"></span> Reject', ['process-approval', 'id' => $model->idpost], [
                        'class' => 'btn btn-danger btn-sm',
                        'style' => 'font-weight: bold;',
                        'data' => [
                            'confirm' => 'Yakin ingin menolak artikel ini? (Untuk memberi catatan spesifik, gunakan halaman Review Detail)',
                            'method' => 'post',
                            'params' => ['action' => 'reject', 'admin_note' => 'Ditolak oleh Admin tanpa catatan spesifik.']
                        ],
                    ]);

                    $detailBtn = '<div style="margin-top: 8px;">' . Html::a('Review Detail & Beri Catatan', ['view', 'id' => $model->idpost], ['class' => 'btn btn-default btn-xs', 'style' => 'color: #666; font-weight: 600; border-radius: 12px;']) . '</div>';

                    return $approveBtn . $rejectBtn . $detailBtn;
                }
            ],
        ],
    ]); ?>

    <!-- BAGIAN KOMENTAR DILAPORKAN -->
    <?php if (isset($commentDataProvider) && $commentDataProvider->getTotalCount() > 0): ?>
        <div style="margin-top: 50px; margin-bottom: 25px;">
            <h3 style="color: #d9534f; font-weight: 800; margin: 0;">
                <span class="glyphicon glyphicon-flag" style="margin-right: 10px;"></span> Komentar Dilaporkan
            </h3>
        </div>

        <?= GridView::widget([
            'dataProvider' => $commentDataProvider,
            'tableOptions' => ['class' => 'table table-hover table-bordered', 'style' => 'background: #fff; border-radius: 8px; overflow: hidden;'],
            'summary' => '',
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                [
                    'attribute' => 'content',
                    'label' => 'Isi Komentar Dilaporkan',
                    'format' => 'raw',
                    'value' => function($model) {
                        return '<p style="margin: 0; color: #333;">"' . Html::encode($model->content) . '"</p>' .
                               '<small style="color: #888;">Ditulis oleh <b>' . Html::encode($model->username) . '</b> di artikel ' . 
                               Html::a('Lihat Artikel', ['view', 'id' => $model->post_id], ['style' => 'color: #2FA084; font-weight: bold;']) . '</small>';
                    }
                ],
                [
                    'label' => 'Tindakan',
                    'format' => 'raw',
                    'contentOptions' => ['style' => 'width: 150px; text-align: center; vertical-align: middle;'],
                    'value' => function($model) {
                        // SWEETALERT DELETE COMMENT
                        return Html::a('<span class="glyphicon glyphicon-trash"></span> Hapus', ['delete-comment', 'id' => $model->id], [
                            'class' => 'btn btn-danger btn-sm',
                            'style' => 'font-weight: bold;',
                            'data' => [
                                'confirm' => 'Yakin ingin menghapus komentar yang dilaporkan ini?',
                                'method' => 'post',
                            ],
                        ]);
                    }
                ],
            ],
        ]); ?>
    <?php endif; ?>
</div>