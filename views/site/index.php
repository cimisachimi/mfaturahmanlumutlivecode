<?php

use yii\helpers\Html;
use yii\helpers\StringHelper;

/** @var yii\web\View $this */
/** @var app\models\Post[] $posts */

$this->title = 'Home - My Blog';
?>
<div class="site-index">

    <!-- Header Banner -->
    <div class="jumbotron text-center" style="background-color: #FFFFFF; border-bottom: 3px solid #2FA084; padding: 40px 20px; margin-bottom: 40px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
        <h1 style="color: #008000; font-weight: 800; font-size: 3em;">Selamat Datang!</h1>
        <p class="lead" style="color: #555; font-size: 1.2em;">Temukan berbagai artikel dan pembaruan terbaru di sini.</p>
    </div>

    <!-- Artikel Grid -->
    <div class="body-content">
        <div class="row">
            <?php if (!empty($posts)): ?>
                <?php foreach ($posts as $post): ?>
                    
                    <div class="col-md-4 col-sm-6" style="margin-bottom: 30px;">
                        <!-- Card Desain -->
                        <div class="post-card" style="background-color: #FFFFFF; border-radius: 8px; border: 1px solid #eee; padding: 25px; box-shadow: 0 4px 10px rgba(0,0,0,0.08); height: 100%; display: flex; flex-direction: column;">
                            
                            <!-- Judul Artikel -->
                            <h3 style="color: #008000; margin-top: 0; font-weight: bold; line-height: 1.4;">
                                <?= Html::encode($post->title) ?>
                            </h3>
														<!-- Menampilkan Thumbnail Jika Ada -->
                            <?php if ($post->thumbnail): ?>
                                <div style="margin: -25px -25px 20px -25px; overflow: hidden; border-radius: 8px 8px 0 0; border-bottom: 3px solid #2FA084;">
                                    <img src="<?= Yii::getAlias('@web/uploads/') . $post->thumbnail ?>" style="width: 100%; height: 200px; object-fit: cover;">
                                </div>
                            <?php endif; ?>
                            
                            <!-- Judul Artikel -->
                            <h3 style="color: #008000; margin-top: 0; font-weight: bold; line-height: 1.4;">
                            
                            <!-- Meta Data (Penulis & Tanggal) -->
                            <div style="color: #888; font-size: 12px; margin-bottom: 15px; border-bottom: 1px solid #2FA084; padding-bottom: 10px;">
                                <span class="glyphicon glyphicon-user" style="color: #2FA084;"></span> 
                                <?= Html::encode($post->username) ?> &nbsp;&nbsp;
                                
                                <span class="glyphicon glyphicon-calendar" style="color: #2FA084;"></span> 
                                <?= Html::encode(date('d M Y', strtotime($post->date))) ?>
                            </div>
                            
                            <!-- Preview Konten -->
                            <div style="color: #444; font-size: 14px; line-height: 1.6; margin-bottom: 20px; flex-grow: 1;">
                                <!-- Menghapus tag HTML bawaan WYSIWYG dan memotong teks agar seragam 30 kata -->
                                <?= StringHelper::truncateWords(strip_tags($post->content), 30) ?>
                            </div>
                            
                            <!-- Label Status Approved -->
                            <div style="text-align: right;">
                                <span class="label" style="background-color: #2FA084; padding: 6px 12px; border-radius: 4px; font-size: 11px;">
                                    Verified Article
                                </span>
                            </div>
                            
                        </div>
                    </div>

                <?php endforeach; ?>
            <?php else: ?>
                <!-- Empty State jika belum ada artikel -->
                <div class="col-md-12 text-center" style="padding: 50px 0;">
                    <span class="glyphicon glyphicon-inbox" style="font-size: 4em; color: #ccc; margin-bottom: 20px;"></span>
                    <h3 style="color: #008000;">Belum Ada Artikel</h3>
                    <p style="color: #777;">Belum ada artikel yang diterbitkan atau disetujui saat ini.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>