<?php
use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'Riwayat Hapus (Trash)';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="post-trash" style="max-width: 1000px; margin: 0 auto; background: #ffffff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.04);">

    <div style="margin-bottom: 25px;">
        <h1 style="color: #d9534f; font-weight: 800; margin: 0;">
            <span class="glyphicon glyphicon-trash" style="margin-right: 10px;"></span> <?= Html::encode($this->title) ?>
        </h1>
        <p style="color: #666; margin-top: 5px;">Daftar artikel yang telah dihapus. Anda dapat mengembalikannya atau menghapusnya secara permanen.</p>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-striped table-bordered', 'style' => 'background: #fff; border-radius: 8px; overflow: hidden;'],
        'summary' => '<div style="margin-bottom: 15px; color: #666; font-size: 13px;">Menampilkan <b>{begin}-{end}</b> dari total <b>{totalCount}</b> artikel terhapus.</div>',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'title',
                'label' => 'Judul Artikel',
                'format' => 'raw',
                'value' => function($model) {
                    return '<strong>' . Html::encode($model->title) . '</strong><br><small style="color: #999;">Oleh: ' . Html::encode($model->username) . '</small>';
                }
            ],
            [
                'label' => 'Aksi Admin',
                'format' => 'raw',
                'contentOptions' => ['style' => 'width: 250px; text-align: center; vertical-align: middle;'],
                'value' => function($model) {
                    $restoreBtn = Html::a('<span class="glyphicon glyphicon-refresh"></span> Restore', ['restore', 'id' => $model->idpost], [
                        'class' => 'btn btn-success btn-sm',
                        'style' => 'margin-right: 5px; font-weight: bold;',
                        'data' => [
                            'confirm' => 'Yakin ingin mengembalikan artikel ini?',
                            'method' => 'post',
                        ],
                    ]);
                    
                    $deleteBtn = Html::a('<span class="glyphicon glyphicon-remove"></span> Hapus Permanen', ['force-delete', 'id' => $model->idpost], [
                        'class' => 'btn btn-danger btn-sm',
                        'style' => 'font-weight: bold;',
                        'data' => [
                            'confirm' => 'PERINGATAN! Artikel ini akan dihapus selamanya dari database. Lanjutkan?',
                            'method' => 'post',
                        ],
                    ]);

                    return $restoreBtn . $deleteBtn;
                }
            ],
        ],
    ]); ?>
</div>