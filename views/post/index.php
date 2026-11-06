<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Manajemen Artikel';
$this->params['breadcrumbs'][] = $this->title;

$username = Yii::$app->user->identity->username;
$roleName = isset(Yii::$app->user->identity->role) ? Yii::$app->user->identity->role : 'Author';
$isAdmin = (strtolower($roleName) === 'admin' || strtolower($username) === 'admin' || strpos(strtolower($username), 'adm') === 0);
?>
<div class="post-index">

    <div class="row" style="margin-bottom: 20px; align-items: center; display: flex;">
        <div class="col-sm-6">
            <h1 style="color: #008000; font-weight: 800; margin: 0;"><?= Html::encode($this->title) ?></h1>
            <p style="color: #666; margin: 5px 0 0 0;">Kelola publikasi artikel, status review, dan konten Anda di sini.</p>
        </div>
        <div class="col-sm-6 text-right">
            <?= Html::a('<span class="glyphicon glyphicon-plus" style="margin-right: 5px;"></span> Buat Artikel Baru', ['create'], [
                'class' => 'btn',
                'style' => 'background-color: #2FA084; color: #FFFFFF; font-weight: 600; border-radius: 20px; padding: 10px 20px;'
            ]) ?>
        </div>
    </div>

    <div class="panel panel-default" style="border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border-radius: 8px; overflow: hidden;">
        <div class="panel-body" style="padding: 0;">
            
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'tableOptions' => ['class' => 'table table-striped table-hover', 'style' => 'margin-bottom: 0;'],
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],

                    [
                        'attribute' => 'title',
                        'label' => 'Judul Artikel',
                        'contentOptions' => ['style' => 'font-weight: 600; color: #333; vertical-align: middle;'],
                    ],
                    [
                        'attribute' => 'date',
                        'label' => 'Tanggal Dibuat',
                        'contentOptions' => ['style' => 'color: #666; vertical-align: middle; width: 160px;'],
                        'value' => function($model) {
                            return date('d M Y, H:i', strtotime($model->date));
                        }
                    ],
                    [
                        'attribute' => 'username',
                        'label' => 'Penulis',
                        'visible' => $isAdmin,
                        'contentOptions' => ['style' => 'font-weight: bold; color: #2FA084; vertical-align: middle;'],
                    ],
                    [
                        'attribute' => 'status',
                        'label' => 'Status & Keterangan',
                        'format' => 'raw',
                        'contentOptions' => ['style' => 'vertical-align: middle; min-width: 220px;'],
                        'value' => function ($model) {
                            $statusBadge = '';
                            switch (strtolower($model->status)) {
                                case 'approved':
                                    $statusBadge = '<span style="background-color: #2FA084; color: white; padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: bold;">APPROVED</span>';
                                    break;
                                case 'pending':
                                    $statusBadge = '<span style="background-color: #f0ad4e; color: white; padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: bold;">PENDING REVIEW</span>';
                                    break;
                                case 'reject':
                                    $statusBadge = '<span style="background-color: #d9534f; color: white; padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: bold;">REJECTED</span>';
                                    break;
                                default:
                                    $statusBadge = '<span style="background-color: #777; color: white; padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: bold;">DRAFT</span>';
                                    break;
                            }

                            $noteHtml = '';
                            if (strtolower($model->status) === 'reject' && !empty($model->admin_note)) {
                                $noteHtml = '<div style="margin-top: 6px; font-size: 12px; color: #d9534f; background: #fdf7f7; padding: 5px 8px; border-left: 3px solid #d9534f; border-radius: 3px;">'
                                          . '<strong>Alasan Ditolak:</strong> ' . Html::encode($model->admin_note)
                                          . '</div>';
                            }

                            return $statusBadge . $noteHtml;
                        },
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'header' => 'Aksi',
                        'contentOptions' => ['style' => 'text-align: center; vertical-align: middle; width: 170px; white-space: nowrap;'],
                        // Mengaktifkan kembali tombol submit (kirim review), view, update (edit), dan delete
                        'template' => '<div class="btn-group" role="group">{submit} {view} {update} {delete}</div>',
                        'buttons' => [
                            // Tombol Submit untuk Review (Hanya muncul jika status Draft atau Reject)
                            'submit' => function ($url, $model, $key) {
                                if (in_array(strtolower($model->status), ['draft', 'reject'])) {
                                    return Html::a('<span class="glyphicon glyphicon-send"></span>', ['submit-review', 'id' => $model->idpost], [
                                        'title' => 'Kirim untuk Review',
                                        'class' => 'btn btn-default btn-sm',
                                        'style' => 'color: #2FA084; border-color: #ddd;',
                                        'data-confirm' => 'Yakin ingin mensubmit artikel ini agar direview oleh Admin?',
                                    ]);
                                }
                                return '';
                            },
                            'view' => function ($url, $model) {
                                return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['view', 'id' => $model->idpost], [
                                    'title' => 'Lihat Artikel',
                                    'class' => 'btn btn-default btn-sm',
                                    'style' => 'color: #333; border-color: #ddd;',
                                ]);
                            },
                            // Tombol Edit (Update) - Muncul untuk mengedit artikel
                            'update' => function ($url, $model) {
                                return Html::a('<span class="glyphicon glyphicon-pencil"></span>', ['update', 'id' => $model->idpost], [
                                    'title' => 'Edit Artikel',
                                    'class' => 'btn btn-default btn-sm',
                                    'style' => 'color: #337ab7; border-color: #ddd;',
                                ]);
                            },
                            'delete' => function ($url, $model) {
                                return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                                    'title' => 'Hapus',
                                    'class' => 'btn btn-default btn-sm',
                                    'style' => 'color: #d9534f; border-color: #ddd;',
                                    'data-confirm' => 'Apakah Anda yakin ingin menghapus artikel ini?',
                                    'data-method' => 'post',
                                ]);
                            },
                        ],
                    ],
                ],
            ]); ?>

        </div>
    </div>
</div>