<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\assets\AppAsset;
use app\widgets\Alert;
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
    
    <!-- Import Font Modern 'Inter' -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;800&display=swap" rel="stylesheet">
    
    <?php $this->head() ?>
    
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            background-color: #f9fbfa; 
        }

        /* --- STYLING NAVBAR --- */
        .navbar-inverse {
            background-color: #FFFFFF !important;
            border: none !important;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
        }

        .navbar-nav > li > a {
            padding-top: 15px !important;
            padding-bottom: 15px !important;
            color: #333 !important;
            font-weight: 600;
            font-size: 15px;
        }

        /* MATIKAN PAKSA GARIS BAWAH YANG BIKIN RUSAK */
        .navbar-nav > li.active > a,
        .navbar-nav > .active > a,
        .navbar-nav > li.active > a:focus,
        .navbar-nav > li.active > a:hover {
            color: #008000 !important;
            background-color: transparent !important;
            border-bottom: none !important; /* Hapus border */
        }
        
        .navbar-nav > li > a:after,
        .navbar-nav > li.active > a:after {
            display: none !important; /* Hapus garis melayang */
            content: none !important;
        }

        /* Efek Sentuh (Hover) Tombol CTA */
        .login-cta > a:hover {
            background-color: #24826b !important;
            color: #FFFFFF !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(47,160,132,0.4) !important;
        }
        .signup-cta > a:hover {
            background-color: #006600 !important;
            color: #FFFFFF !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0,128,0,0.3) !important;
        }

        .navbar-brand {
            display: flex !important;
            align-items: center !important;
            height: 50px;
        }
        
        .navbar-form {
            padding: 0 !important;
            margin: 0 !important;
            display: flex !important;
            align-items: center !important;
            height: 50px;
        }
        
        .user-info-nav {
            display: flex !important;
            align-items: center !important;
            padding: 0 15px !important;
            height: 50px !important;
            color: #555;
            font-size: 14px;
        }
    </style>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => '<span class="glyphicon glyphicon-leaf" style="color: #2FA084; margin-right: 8px; font-size: 1.1em;"></span><span style="color: #008000; font-weight: 800; font-size: 1.3em; letter-spacing: -0.5px;">LUMUT<span style="color: #2FA084;">BLOG</span></span>',
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
        // STYLE INLINE ABSOLUT UNTUK MENCEGAH BENTROK CSS[cite: 6]
        $menuItems[] = [
            'label' => '<span class="glyphicon glyphicon-log-in" style="margin-right: 5px;"></span> Login', 
            'url' => ['/site/login'],
            'options' => ['class' => 'login-cta'],
            'linkOptions' => [
                'style' => 'background-color: #2FA084 !important; color: #FFFFFF !important; border-radius: 25px !important; padding: 8px 22px !important; margin: 8px 5px !important; font-weight: 600 !important; font-size: 14px !important; display: inline-block !important; line-height: 1.5 !important;'
            ]
        ];
        $menuItems[] = [
            'label' => '<span class="glyphicon glyphicon-user" style="margin-right: 5px;"></span> Signup', 
            'url' => ['/site/signup'],
            'options' => ['class' => 'signup-cta'],
            'linkOptions' => [
                'style' => 'background-color: #008000 !important; color: #FFFFFF !important; border-radius: 25px !important; padding: 8px 22px !important; margin: 8px 5px !important; font-weight: 600 !important; font-size: 14px !important; display: inline-block !important; line-height: 1.5 !important;'
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
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer" style="background-color: #FFFFFF; border-top: 1px solid #eaeaea; padding: 25px 0; margin-top: 40px;">
    <div class="container">
        <p class="pull-left" style="color: #666; font-weight: 500;">&copy; LumutBlog By <?= Html::encode('Muhammad Faturahman') ?> <?= date('Y') ?></p>
        <p class="pull-right" style="color: #aaa; font-size: 13px;">Powered by Yii2</p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>