<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Posts';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="post-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Post', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'title:ntext',
            'content:html',
            'date',
            'username',
            
            // Kolom Status dengan Badge Warna
            [
                'attribute' => 'status',
                'format' => 'raw',
                'value' => function ($model) {
                    $colors = [
                        'draft' => 'default',   // Abu-abu
                        'pending' => 'info',    // Biru Muda
                        'approved' => 'success',// Hijau (Sesuai tema Anda)
                        'reject' => 'danger',   // Merah
                    ];
                    
                    // Ambil warna berdasarkan status, jika tidak ada fallback ke default
                    $color = isset($colors[$model->status]) ? $colors[$model->status] : 'default';
                    
                    // Huruf kapital di awal kata
                    $label = ucfirst($model->status);
                    
                    return "<span class=\"label label-{$color}\">{$label}</span>";
                },
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>