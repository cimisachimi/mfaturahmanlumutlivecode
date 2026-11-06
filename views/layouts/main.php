<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\assets\AppAsset;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;800&display=swap" rel="stylesheet">
    
    <?php $this->head() ?>
    
    <style>
        html, body { 
            font-family: 'Inter', sans-serif; 
            background-color: #E5EDDA !important;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
        }
        
        .wrap, .wrap > .container, .site-index {
            background-color: transparent !important; 
            flex: 1 0 auto;
        }

        .navbar-inverse {
            background-color: #FFFFFF !important;
            border: none !important;
            box-shadow: 0 4px 15px rgba(0,0,0,0.04);
        }

        .navbar-nav > li > a {
            padding-top: 15px !important;
            padding-bottom: 15px !important;
            color: #333 !important;
            font-weight: 600;
            font-size: 14px;
        }

        .navbar-nav > li.active > a,
        .navbar-nav > .active > a,
        .navbar-nav > li.active > a:focus,
        .navbar-nav > li.active > a:hover {
            color: #008000 !important;
            background-color: transparent !important;
            border-bottom: none !important;
        }
        
        .navbar-nav > li > a:after,
        .navbar-nav > li.active > a:after {
            display: none !important;
            content: none !important;
        }

        .login-cta > a:hover {
            background-color: #2FA084 !important;
            color: #FFFFFF !important;
        }
        .signup-cta > a:hover {
            background-color: #24826b !important;
            box-shadow: 0 4px 10px rgba(47,160,132,0.3) !important;
        }

        .navbar-brand, .navbar-form {
            display: flex !important;
            align-items: center !important;
            height: 50px;
            padding: 0 !important;
            margin: 0 !important;
        }
        
        .user-info-nav {
            display: flex !important;
            align-items: center !important;
            padding: 0 15px !important;
            height: 50px !important;
            color: #555;
            font-size: 14px;
        }

        /* --- STYLING KHUSUS POP-UP SWEETALERT2 TEMA LUMUTBLOG --- */
        .swal-custom-popup {
            font-family: 'Inter', sans-serif !important;
            border-radius: 16px !important;
            padding: 25px !important;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1) !important;
        }
        .swal-custom-title {
            color: #008000 !important;
            font-weight: 800 !important;
            font-size: 24px !important;
        }
        .swal-custom-confirm {
            border-radius: 25px !important;
            padding: 10px 30px !important;
            font-weight: 700 !important;
            letter-spacing: 0.5px !important;
            box-shadow: 0 4px 10px rgba(47,160,132,0.3) !important;
        }
        .swal-custom-cancel {
            border-radius: 25px !important;
            padding: 10px 30px !important;
            font-weight: 700 !important;
            background-color: #f5f5f5 !important;
            color: #555 !important;
        }
    </style>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
<?php
    NavBar::begin([
        // Menggabungkan tag <img> untuk logo PNG dan <span> untuk teks teks LUMUTBLOG
        'brandLabel' => '<img src="' . Yii::$app->request->baseUrl . '/images/logo.png" alt="Logo" style="height: 28px; margin-right: 10px;">' .
                        '<span style="color: #008000; font-weight: 800; font-size: 1.3em; letter-spacing: -0.5px;">LUMUT<span style="color: #2FA084;">BLOG</span></span>',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
            'style' => 'padding: 5px 0;'
        ],
    ]);
    
    $menuItems = [
        ['label' => 'Beranda', 'url' => ['/site/index']],
    ];
    
    if (Yii::$app->user->isGuest) {
        $menuItems[] = [
            'label' => '<span class="glyphicon glyphicon-log-in" style="margin-right: 5px;"></span> Login', 
            'url' => ['/site/login'],
            'options' => ['class' => 'login-cta'],
            'linkOptions' => [
                'style' => 'background-color: transparent !important; color: #2FA084 !important; border: 2px solid #2FA084 !important; border-radius: 25px !important; padding: 6px 20px !important; margin: 8px 5px !important; font-weight: 600 !important; font-size: 13px !important; display: inline-block !important; transition: all 0.3s ease;'
            ]
        ];
        $menuItems[] = [
            'label' => '<span class="glyphicon glyphicon-user" style="margin-right: 5px;"></span> Signup', 
            'url' => ['/site/signup'],
            'options' => ['class' => 'signup-cta'],
            'linkOptions' => [
                'style' => 'background-color: #2FA084 !important; color: #FFFFFF !important; border: 2px solid #2FA084 !important; border-radius: 25px !important; padding: 6px 20px !important; margin: 8px 5px !important; font-weight: 600 !important; font-size: 13px !important; display: inline-block !important; transition: all 0.3s ease;'
            ]
        ];
    } else {
        $menuItems[] = ['label' => 'Manajemen Post', 'url' => ['/post/index']];
        
        $username = Yii::$app->user->identity->username;
        $roleName = isset(Yii::$app->user->identity->role) ? Yii::$app->user->identity->role : ($username === 'admin' ? 'Admin' : 'Author');
        $isAdmin = strtolower($roleName) === 'admin';

        if ($isAdmin) {
            $pendingCount = \app\models\Post::find()->where(['status' => 'pending'])->count();
            $badgeNotif = $pendingCount > 0 ? ' <span class="badge" style="background-color: #d9534f; margin-left: 3px; font-size: 11px; vertical-align: middle;">' . $pendingCount . '</span>' : '';
            
            $menuItems[] = [
                'label' => '<span class="glyphicon glyphicon-bell" style="margin-right: 4px; color: #2FA084;"></span> Approval' . $badgeNotif, 
                'url' => ['/post/approval']
            ];
            
            $menuItems[] = [
                'label' => '<span class="glyphicon glyphicon-trash" style="margin-right: 4px; color: #d9534f;"></span> Riwayat Hapus', 
                'url' => ['/post/trash']
            ];
        }

        if ($isAdmin) {
            $badgeStyle = 'background-color: #008000; color: #FFFFFF; padding: 3px 10px; border-radius: 12px; font-size: 11px; margin-left: 8px; font-weight: bold; letter-spacing: 0.5px; text-transform: uppercase; box-shadow: 0 2px 4px rgba(0,128,0,0.2);';
        } else {
            $badgeStyle = 'background-color: #2FA084; color: #FFFFFF; padding: 3px 10px; border-radius: 12px; font-size: 11px; margin-left: 8px; font-weight: bold; letter-spacing: 0.5px; text-transform: uppercase;';
        }
        
        $roleBadge = '<span style="' . $badgeStyle . '">' . Html::encode($roleName) . '</span>';
        
        $menuItems[] = '<li class="user-info-nav">'
            . '<span class="glyphicon glyphicon-user" style="color: #2FA084; margin-right: 6px; font-size: 16px;"></span> ' 
            . '<span style="font-weight: 600; color: #008000;">' . Html::encode($username) . '</span>'
            . $roleBadge 
            . '</li>';
            
        $menuItems[] = '<li>'
            . Html::beginForm(['/site/logout'], 'post', ['class' => 'navbar-form'])
            . Html::submitButton(
                '<span class="glyphicon glyphicon-log-out" style="margin-right: 5px;"></span> Keluar',
                ['class' => 'btn btn-link logout', 'style' => 'color: #e74c3c; font-weight: 600; text-decoration: none; font-size: 14px; padding: 0 15px; display: flex; align-items: center;']
            )
            . Html::endForm()
            . '</li>';
    }
    
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => $menuItems,
        'encodeLabels' => false, 
    ]);
    
    NavBar::end();
    ?>

    <div class="container" style="margin-top: 75px;">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        
        <!-- ALERT STANDAR YII2 DIHAPUS, DIGANTI DENGAN SCRIPT SWEETALERT2 DI BAWAH -->
        <?= $content ?>
    </div>
</div>

<footer class="footer" style="height: auto !important; min-height: 200px; flex-shrink: 0; background-color: #004d00 !important; color: #ffffff !important; padding: 50px 0 20px 0; margin-top: 60px; border-top: none;">
    <div class="container">
        <div class="row" style="margin-bottom: 30px;">
            <div class="col-md-4" style="margin-bottom: 20px;">
                <h4 style="color: #2FA084; font-weight: 700; margin-bottom: 15px; text-transform: uppercase; font-size: 16px;">Tentang LumutBlog</h4>
                <p style="color: #cccccc; font-size: 14px; line-height: 1.6;">
                    LumutBlog adalah platform publikasi artikel modern yang dirancang untuk memberikan pengalaman menulis dan membaca yang bersih, cepat, dan bernuansa alam.
                </p>
            </div>
            
            <div class="col-md-4" style="margin-bottom: 20px;">
                <h4 style="color: #2FA084; font-weight: 700; margin-bottom: 15px; text-transform: uppercase; font-size: 16px;">Kontak Kami</h4>
                <ul style="list-style: none; padding: 0; color: #cccccc; font-size: 14px; line-height: 1.8;">
                    <li><span class="glyphicon glyphicon-envelope" style="margin-right: 10px; color: #2FA084;"></span> info@lumutblog.com</li>
                    <li><span class="glyphicon glyphicon-earphone" style="margin-right: 10px; color: #2FA084;"></span> +62 812 3456 7890</li>
                </ul>
            </div>
            
            <div class="col-md-4" style="margin-bottom: 20px;">
                <h4 style="color: #2FA084; font-weight: 700; margin-bottom: 15px; text-transform: uppercase; font-size: 16px;">Alamat</h4>
                <ul style="list-style: none; padding: 0; color: #cccccc; font-size: 14px; line-height: 1.6;">
                    <li><span class="glyphicon glyphicon-map-marker" style="margin-right: 10px; color: #2FA084;"></span> Diro RT 057, Pendowoharjo,</li>
                    <li style="padding-left: 24px;">Sewon, Bantul,</li>
                    <li style="padding-left: 24px;">Daerah Istimewa Yogyakarta, Indonesia</li>
                </ul>
            </div>
        </div>
        
        <div style="border-top: 1px solid rgba(255,255,255,0.1); padding-top: 20px; text-align: center; color: #999999; font-size: 13px;">
            &copy; LumutBlog By <?= Html::encode('Muhammad Faturahman') ?> <?= date('Y') ?> &bull; Powered by Yii2
        </div>
    </div>
</footer>

<?php $this->endBody() ?>

<!-- ========================================== -->
<!-- INTEGRASI SWEETALERT2 UNTUK POP UP & ALERT -->
<!-- ========================================== -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // 1. OVERRIDE CONFIRMATION BOX BAWAAN YII2 (Untuk Tombol Hapus, Restore, dll)
        if (typeof yii !== 'undefined') {
            yii.confirm = function (message, okCallback, cancelCallback) {
                // Deteksi apakah ini aksi destruktif (mengandung kata hapus/permanen) untuk mengubah warna tombol
                var isDanger = message.toLowerCase().includes('hapus') || message.toLowerCase().includes('tolak');
                
                Swal.fire({
                    title: 'Konfirmasi Tindakan',
                    text: message,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: isDanger ? '#d9534f' : '#2FA084', // Merah jika hapus, Hijau jika aman
                    cancelButtonColor: '#e0e0e0',
                    confirmButtonText: 'Ya, Lanjutkan',
                    cancelButtonText: 'Batal',
                    reverseButtons: true, // Tombol konfirmasi di kanan
                    customClass: {
                        popup: 'swal-custom-popup',
                        title: 'swal-custom-title',
                        confirmButton: 'swal-custom-confirm',
                        cancelButton: 'swal-custom-cancel'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        okCallback();
                    } else if (cancelCallback) {
                        cancelCallback();
                    }
                });
            };
        }

        // 2. MENGUBAH FLASH MESSAGES YII2 MENJADI POP UP CANTIK
        <?php
        $session = Yii::$app->session;
        $flashes = $session->getAllFlashes();
        $jsFlashes = '';
        foreach ($flashes as $type => $data) {
            $messages = (array) $data;
            // Konversi tipe alert Yii ke tipe icon SweetAlert
            $swalType = ($type === 'danger' || $type === 'error') ? 'error' : (($type === 'success') ? 'success' : 'info');
            $title = ($swalType === 'error') ? 'Gagal!' : (($swalType === 'success') ? 'Berhasil!' : 'Informasi');
            
            foreach ($messages as $message) {
                $msg = addslashes($message);
                $jsFlashes .= "Swal.fire({ 
                    title: '{$title}', 
                    text: '{$msg}', 
                    icon: '{$swalType}', 
                    confirmButtonColor: '#2FA084', 
                    confirmButtonText: 'Tutup',
                    customClass: { 
                        popup: 'swal-custom-popup', 
                        title: 'swal-custom-title',
                        confirmButton: 'swal-custom-confirm' 
                    } 
                });\n";
            }
            $session->removeFlash($type);
        }
        echo $jsFlashes;
        ?>
    });
</script>

</body>
</html>
<?php $this->endPage() ?>