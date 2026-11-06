<?php

/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'LumutBlog - Beranda';
?>
<div class="site-index">

    <!-- Jumbotron Banner -->
    <div class="jumbotron" style="background: linear-gradient(135deg, #008000 0%, #2FA084 100%); color: white; border-radius: 12px; padding: 50px 30px; margin-bottom: 40px; box-shadow: 0 10px 30px rgba(0,128,0,0.15);">
        <h1 style="font-weight: 800; font-size: 42px; letter-spacing: -1px;">Selamat Datang di LumutBlog</h1>
        <p class="lead" style="font-size: 18px; opacity: 0.9; margin-top: 15px;">
            Platform publikasi artikel modern dengan arsitektur bersih, cepat, dan bernuansa hijau alami.
        </p>
        <?php if (Yii::$app->user->isGuest): ?>
            <p style="margin-top: 25px;">
                <a class="btn btn-lg" href="<?= Url::to(['site/login']) ?>" style="background-color: #ffffff; color: #008000; font-weight: bold; border-radius: 25px; padding: 12px 30px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">Mulai Menulis</a>
            </p>
        <?php else: ?>
            <p style="margin-top: 25px;">
                <a class="btn btn-lg" href="<?= Url::to(['post/index']) ?>" style="background-color: #ffffff; color: #008000; font-weight: bold; border-radius: 25px; padding: 12px 30px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">Kelola Artikel Saya</a>
            </p>
        <?php endif; ?>
    </div>

    <!-- Daftar Artikel Publik (Hanya yang Approved) -->
    <div class="body-content">
        <h2 style="color: #008000; font-weight: 800; margin-bottom: 30px; border-bottom: 2px solid #2FA084; padding-bottom: 10px; display: inline-block;">Artikel Terbaru</h2>

        <div class="row">
            <?php 
            // Ambil hanya artikel yang sudah approved, diurutkan berdasarkan tanggal review/approval terbaru
            $posts = \app\models\Post::find()->where(['status' => 'approved'])->orderBy(['reviewed_at' => SORT_DESC])->all();
            
            if (empty($posts)): 
            ?>
                <div class="col-lg-12">
                    <div class="alert alert-info" style="border-radius: 8px; padding: 20px;">
                        Belum ada artikel yang dipublikasikan saat ini. Silakan cek kembali nanti!
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($posts as $post): ?>
                    <div class="col-lg-4 col-md-6" style="margin-bottom: 30px;">
                        <div class="panel panel-default" style="border: none; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.06); overflow: hidden; transition: transform 0.3s ease; height: 100%;">
                            
                            <!-- Thumbnail Card (Kini bisa diklik menuju view artikel) -->
                            <?php if (!empty($post->thumbnail)): ?>
                                <div style="height: 200px; overflow: hidden; background: #eee;">
                                    <?= Html::a(
                                        '<img src="' . Yii::$app->request->baseUrl . '/uploads/' . Html::encode($post->thumbnail) . '" alt="Thumbnail" style="width: 100%; height: 100%; object-fit: cover;">',
                                        ['post/view', 'id' => $post->idpost],
                                        ['style' => 'display: block; height: 100%;']
                                    ) ?>
                                </div>
                            <?php else: ?>
                                <div style="height: 100px; background: linear-gradient(45deg, #008000, #2FA084); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                    <?= Html::a(Html::encode('LumutBlog Article'), ['post/view', 'id' => $post->idpost], ['style' => 'color: white; text-decoration: none; display: block; width: 100%; height: 100%; text-align: center; line-height: 100px;']) ?>
                                </div>
                            <?php endif; ?>

                            <div class="panel-body" style="padding: 20px; display: flex; flex-direction: column; justify-content: space-between;">
                                <div>
                                    <?php 
                                        // Gunakan tanggal approved (reviewed_at) jika ada, jika tidak fallback ke tanggal buat (date)
                                        $publishDate = !empty($post->reviewed_at) ? $post->reviewed_at : $post->date;
                                    ?>
                                    <span style="color: #2FA084; font-size: 12px; font-weight: bold; text-transform: uppercase;"><?= date('d M Y', strtotime($publishDate)) ?></span>
                                    
                                    <!-- Judul Artikel (Bisa diklik) -->
                                    <h3 style="color: #008000; font-weight: 700; font-size: 18px; margin-top: 8px; margin-bottom: 12px; line-height: 1.4;">
                                        <?= Html::a(Html::encode($post->title), ['post/view', 'id' => $post->idpost], ['style' => 'color: #008000; text-decoration: none;']) ?>
                                    </h3>
                                    
                                    <p style="color: #666; font-size: 14px; line-height: 1.6;">
                                        <?= \yii\helpers\StringHelper::truncate(strip_tags($post->content), 100, '...') ?>
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
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>