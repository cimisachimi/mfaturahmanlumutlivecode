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

    <!-- Header dengan Flexbox agar tombol Buat Artikel rapi di ujung kanan -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 15px;">
        <div>
            <h1 style="color: #008000; font-weight: 800; margin: 0;"><?= Html::encode($this->title) ?></h1>
            <p style="color: #666; margin: 5px 0 0 0;">Kelola publikasi artikel, status review, dan konten Anda di sini.</p>
        </div>
        <div>
            <?= Html::a('<span class="glyphicon glyphicon-plus" style="margin-right: 5px;"></span> Buat Artikel Baru', ['create'], [
                'class' => 'btn',
                'style' => 'background-color: #2FA084; color: #FFFFFF; font-weight: 600; border-radius: 25px; padding: 10px 20px; box-shadow: 0 4px 10px rgba(47,160,132,0.2); transition: all 0.3s ease;'
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

                    // KOLOM GAMBAR (Sekarang bisa diklik menuju view)
                    [
                        'label' => 'Gambar',
                        'format' => 'raw',
                        'contentOptions' => ['style' => 'text-align: center; vertical-align: middle; width: 100px;'],
                        'value' => function ($model) {
                            if (!empty($model->thumbnail)) {
                                $imgUrl = Yii::$app->request->baseUrl . '/uploads/' . Html::encode($model->thumbnail);
                                $imageHtml = Html::img($imgUrl, [
                                    'style' => 'width: 80px; height: 50px; object-fit: cover; border-radius: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); transition: transform 0.2s;'
                                ]);
                            } else {
                                $imageHtml = '<div style="width: 80px; height: 50px; background-color: #f4f8f1; border-radius: 4px; border: 1px solid #eaeaea; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #2FA084; font-size: 10px; font-weight: 600; margin: 0 auto; transition: transform 0.2s;">
                                            <span class="glyphicon glyphicon-picture" style="font-size: 14px; margin-bottom: 3px; opacity: 0.7;"></span>
                                        </div>';
                            }
                            // Bungkus gambar dengan tautan ke halaman view
                            return Html::a($imageHtml, ['view', 'id' => $model->idpost], ['title' => 'Lihat Artikel', 'style' => 'display: inline-block;']);
                        }
                    ],

                    // KOLOM JUDUL (Sekarang bisa diklik menuju view)
                    [
                        'attribute' => 'title',
                        'label' => 'Judul Artikel',
                        'format' => 'raw',
                        'contentOptions' => ['style' => 'font-weight: 600; vertical-align: middle;'],
                        'value' => function($model) {
                            return Html::a(Html::encode($model->title), ['view', 'id' => $model->idpost], [
                                'style' => 'color: #333; text-decoration: none; font-size: 14px;',
                                'onmouseover' => 'this.style.color="#2FA084"',
                                'onmouseout' => 'this.style.color="#333"'
                            ]);
                        }
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
                    
                    // KOLOM AKSI (Warna dibedakan & btn-group dihapus agar renggang rapi)
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'header' => 'Aksi',
                        'contentOptions' => ['style' => 'text-align: center; vertical-align: middle; width: 170px; white-space: nowrap;'],
                        'template' => '<div style="display: flex; justify-content: center; gap: 5px;">{submit} {view} {update} {delete}</div>',
                        'buttons' => [
                            'submit' => function ($url, $model, $key) {
                                if (in_array(strtolower($model->status), ['draft', 'reject'])) {
                                    return Html::a('<span class="glyphicon glyphicon-send"></span>', ['submit-review', 'id' => $model->idpost], [
                                        'title' => 'Kirim untuk Review',
                                        'class' => 'btn btn-primary btn-sm', // Warna Biru
                                        'data-confirm' => 'Yakin ingin mensubmit artikel ini agar direview oleh Admin?',
                                    ]);
                                }
                                return '';
                            },
                            'view' => function ($url, $model) {
                                return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['view', 'id' => $model->idpost], [
                                    'title' => 'Lihat Artikel',
                                    'class' => 'btn btn-info btn-sm', // Warna Info (Teal/Cyan)
                                    'style' => 'background-color: #2FA084; border-color: #2FA084; color: white;'
                                ]);
                            },
                            'update' => function ($url, $model) {
                                return Html::a('<span class="glyphicon glyphicon-pencil"></span>', ['update', 'id' => $model->idpost], [
                                    'title' => 'Edit Artikel',
                                    'class' => 'btn btn-warning btn-sm', // Warna Kuning
                                    'style' => 'color: white;'
                                ]);
                            },
                            'delete' => function ($url, $model) {
                                return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                                    'title' => 'Hapus',
                                    'class' => 'btn btn-danger btn-sm', // Warna Merah
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