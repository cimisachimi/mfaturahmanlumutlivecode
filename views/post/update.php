<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Post */

$this->title = 'Update Post: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Posts', 'url' => ['index']];

// Breadcrumb judul artikel sekarang murni sebagai teks navigasi yang rapi,
// tidak lagi mencoba melink ke halaman view yang memicu error Unknown Property
$this->params['breadcrumbs'][] = $model->title; 
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="post-update">

    <h1 style="color: #008000; margin-bottom: 20px; font-weight: bold;">
        <?= Html::encode($this->title) ?>
    </h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>