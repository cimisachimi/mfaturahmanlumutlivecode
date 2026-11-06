<?php

/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'LumutBlog - Beranda';

// Tangkap parameter filter & view dari URL
$sort = Yii::$app->request->get('sort', 'desc');
$orderDirection = ($sort === 'asc') ? SORT_ASC : SORT_DESC;
$viewMode = Yii::$app->request->get('view', 'box'); // default: box
?>

<!-- Tambahkan sedikit CSS responsif untuk mode list -->
<style>
    @media (max-width: 767px) {
        .list-view-card { flex-direction: column !important; }
        .list-view-card .img-container { width: 100% !important; height: 200px !important; }
    }
</style>

<div class="site-index">

    <!-- HERO SECTION -->
    <div style="background-color: #ffffff; border: 1px solid #dcedc8; border-left: 6px solid #2FA084; border-radius: 12px; padding: 40px 30px; margin-bottom: 40px; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
        <h1 style="font-weight: 800; font-size: 32px; margin-top: 0; margin-bottom: 15px; color: #008000; letter-spacing: -0.5px;">Selamat Datang di LumutBlog</h1>
        <p style="font-size: 16px; color: #666; max-width: 800px; margin-bottom: 25px; line-height: 1.6;">
            Platform publikasi artikel modern dengan arsitektur bersih, cepat, dan bernuansa hijau alami.
        </p>
        
        <?php if (Yii::$app->user->isGuest): ?>
            <a href="<?= Url::to(['site/login']) ?>" style="display: inline-block; background-color: #2FA084; color: #FFFFFF; font-weight: 600; border-radius: 25px; padding: 10px 25px; box-shadow: 0 4px 10px rgba(47,160,132,0.2); text-decoration: none; font-size: 14px; transition: all 0.2s ease;">
                <span class="glyphicon glyphicon-pencil" style="margin-right: 5px;"></span> Mulai Menulis
            </a>
        <?php else: ?>
            <a href="<?= Url::to(['post/index']) ?>" style="display: inline-block; background-color: #2FA084; color: #FFFFFF; font-weight: 600; border-radius: 25px; padding: 10px 25px; box-shadow: 0 4px 10px rgba(238, 245, 243, 0.2); text-decoration: none; font-size: 14px; transition: all 0.2s ease;">
                <span class="glyphicon glyphicon-list-alt" style="margin-right: 5px;"></span> Kelola Artikel Saya
            </a>
        <?php endif; ?>
    </div>

    <!-- Daftar Artikel Publik -->
    <div class="body-content">
        
        <!-- HEADER KONTEN & PANEL KONTROL (VIEW + SORT) -->
        <div style="display: flex; justify-content: space-between; align-items: flex-end; border-bottom: 3px solid #2FA084; margin-bottom: 25px; padding-bottom: 8px;">
            <h2 style="color: #008000; font-weight: 800; margin: 0;">Artikel Terbaru</h2>
            
            <!-- Kontrol View Mode & Sort -->
            <div style="display: flex; align-items: center; gap: 15px;">
                
                <!-- Toggle View Mode -->
                <div class="btn-group" style="box-shadow: 0 2px 5px rgba(0,0,0,0.05); border-radius: 6px;">
                    <a href="<?= Url::to(['site/index', 'sort' => $sort, 'view' => 'box']) ?>" 
                       class="btn btn-sm <?= $viewMode === 'box' ? 'active' : '' ?>" 
                       style="<?= $viewMode === 'box' ? 'background-color: #2FA084; color: white; border: 1px solid #2FA084;' : 'background-color: #fff; color: #666; border: 1px solid #ddd;' ?>" title="Box View">
                        <span class="glyphicon glyphicon-th-large"></span>
                    </a>
                    <a href="<?= Url::to(['site/index', 'sort' => $sort, 'view' => 'list']) ?>" 
                       class="btn btn-sm <?= $viewMode === 'list' ? 'active' : '' ?>" 
                       style="<?= $viewMode === 'list' ? 'background-color: #2FA084; color: white; border: 1px solid #2FA084;' : 'background-color: #fff; color: #666; border: 1px solid #ddd;' ?>" title="List View">
                        <span class="glyphicon glyphicon-th-list"></span>
                    </a>
                </div>

                <!-- Form Dropdown Filter Sort -->
                <form method="get" action="<?= Url::to(['site/index']) ?>" style="margin: 0;">
                    <!-- Bawa parameter view yang sedang aktif agar tidak kereset saat filter sort -->
                    <input type="hidden" name="view" value="<?= Html::encode($viewMode) ?>">
                    
                    <select name="sort" onchange="this.form.submit()" style="padding: 5px 12px; border-radius: 6px; border: 1px solid #ddd; color: #333; font-weight: 600; font-size: 13px; background-color: #fff; cursor: pointer; outline: none; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                        <option value="desc" <?= $sort === 'desc' ? 'selected' : '' ?>> Terbaru</option>
                        <option value="asc" <?= $sort === 'asc' ? 'selected' : '' ?>> Terlama</option>
                    </select>
                </form>
            </div>
        </div>

        <div class="row">
            <?php 
            $posts = \app\models\Post::find()
                        ->where(['status' => 'approved'])
                        ->orderBy(['reviewed_at' => $orderDirection])
                        ->all();
            
            if (empty($posts)): 
            ?>
                <div class="col-lg-12">
                    <div class="alert alert-info" style="border-radius: 8px; padding: 20px;">
                        Belum ada artikel yang dipublikasikan saat ini. Silakan cek kembali nanti!
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($posts as $post): ?>
                    <?php 
                        $publishDate = !empty($post->reviewed_at) ? $post->reviewed_at : $post->date;
                        $shortContent = \yii\helpers\StringHelper::truncate(strip_tags($post->content), 100, '...');
                    ?>

                    <?php if ($viewMode === 'list'): ?>
                        <!-- ========================= LAYOUT LIST (HORIZONTAL) ========================= -->
                        <div class="col-lg-12" style="margin-bottom: 25px;">
                            <div class="panel panel-default list-view-card" style="border: none; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.04); overflow: hidden; display: flex; transition: transform 0.3s ease; background-color: #fff;">
                                
                                <!-- Container Gambar (Kiri) -->
                                <div class="img-container" style="width: 300px; flex-shrink: 0; background-color: #f4f8f1; border-right: 1px solid #eaeaea;">
                                    <?php if (!empty($post->thumbnail)): ?>
                                        <?= Html::a(
                                            '<img src="' . Yii::$app->request->baseUrl . '/uploads/' . Html::encode($post->thumbnail) . '" alt="Thumbnail" style="width: 100%; height: 100%; min-height: 200px; object-fit: cover;">',
                                            ['post/view', 'id' => $post->idpost],
                                            ['style' => 'display: block; height: 100%;']
                                        ) ?>
                                    <?php else: ?>
                                        <div style="height: 100%; min-height: 200px; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #2FA084;">
                                            <?= Html::a(
                                                '<span class="glyphicon glyphicon-picture" style="font-size: 32px; margin-bottom: 10px; opacity: 0.5; color: #2FA084;"></span><br><span style="font-size: 14px; font-weight: 600; opacity: 0.7; color: #2FA084;">Tanpa Gambar</span>', 
                                                ['post/view', 'id' => $post->idpost], 
                                                ['style' => 'text-decoration: none; text-align: center; display: block; width: 100%;']
                                            ) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Container Konten (Kanan) -->
                                <div class="panel-body" style="padding: 25px; flex-grow: 1; display: flex; flex-direction: column; justify-content: center;">
                                    <div>
                                        <span style="color: #2FA084; font-size: 12px; font-weight: bold; text-transform: uppercase;"><?= date('d M Y', strtotime($publishDate)) ?> &bull; Oleh <?= Html::encode($post->username) ?></span>
                                        
                                        <h3 style="color: #008000; font-weight: 700; font-size: 22px; margin-top: 8px; margin-bottom: 12px; line-height: 1.4;">
                                            <?= Html::a(Html::encode($post->title), ['post/view', 'id' => $post->idpost], ['style' => 'color: #008000; text-decoration: none;']) ?>
                                        </h3>
                                        
                                        <p style="color: #666; font-size: 15px; line-height: 1.6; margin-bottom: 15px;">
                                            <?= $shortContent ?>
                                        </p>
                                    </div>

                                    <div>
                                        <?= Html::a('Baca Selengkapnya &rarr;', ['post/view', 'id' => $post->idpost], [
                                            'class' => 'btn btn-sm',
                                            'style' => 'background-color: #f4f8f1; color: #2FA084; font-weight: bold; border-radius: 20px; padding: 6px 15px; border: 1px solid #dcedc8;'
                                        ]) ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <?php else: ?>
                        <!-- ========================= LAYOUT BOX/GRID (VERTIKAL) ========================= -->
                        <div class="col-lg-4 col-md-6" style="margin-bottom: 30px;">
                            <div class="panel panel-default" style="border: none; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.04); overflow: hidden; transition: transform 0.3s ease; height: 100%; display: flex; flex-direction: column; background-color: #fff;">
                                
                                <?php if (!empty($post->thumbnail)): ?>
                                    <div style="height: 200px; overflow: hidden; background: #eee;">
                                        <?= Html::a(
                                            '<img src="' . Yii::$app->request->baseUrl . '/uploads/' . Html::encode($post->thumbnail) . '" alt="Thumbnail" style="width: 100%; height: 100%; object-fit: cover;">',
                                            ['post/view', 'id' => $post->idpost],
                                            ['style' => 'display: block; height: 100%;']
                                        ) ?>
                                    </div>
                                <?php else: ?>
                                    <div style="height: 200px; background-color: #f4f8f1; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #2FA084; border-bottom: 1px solid #eaeaea;">
                                        <?= Html::a(
                                            '<span class="glyphicon glyphicon-picture" style="font-size: 32px; margin-bottom: 10px; opacity: 0.5; color: #2FA084;"></span><br><span style="font-size: 14px; font-weight: 600; opacity: 0.7; color: #2FA084;">Tanpa Gambar</span>', 
                                            ['post/view', 'id' => $post->idpost], 
                                            ['style' => 'text-decoration: none; text-align: center; display: block; width: 100%;']
                                        ) ?>
                                    </div>
                                <?php endif; ?>

                                <div class="panel-body" style="padding: 20px; display: flex; flex-direction: column; justify-content: space-between; flex-grow: 1;">
                                    <div>
                                        <span style="color: #2FA084; font-size: 12px; font-weight: bold; text-transform: uppercase;"><?= date('d M Y', strtotime($publishDate)) ?></span>
                                        
                                        <h3 style="color: #008000; font-weight: 700; font-size: 18px; margin-top: 8px; margin-bottom: 12px; line-height: 1.4;">
                                            <?= Html::a(Html::encode($post->title), ['post/view', 'id' => $post->idpost], ['style' => 'color: #008000; text-decoration: none;']) ?>
                                        </h3>
                                        
                                        <p style="color: #666; font-size: 14px; line-height: 1.6;">
                                            <?= $shortContent ?>
                                        </p>
                                    </div>

                                    <div style="margin-top: 20px; padding-top: 15px; border-top: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center;">
                                        <span style="font-size: 13px; color: #888;">Oleh <strong><?= Html::encode($post->username) ?></strong></span>
                                        <?= Html::a('Baca Selengkapnya &rarr;', ['post/view', 'id' => $post->idpost], [
                                            'style' => 'color: #2FA084; font-weight: bold; font-size: 13px; text-decoration: none;'
                                        ]) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>