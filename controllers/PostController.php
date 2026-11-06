<?php

namespace app\controllers;

use Yii;
use app\models\Post;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * PostController implements the CRUD actions for Post model.
 */
class PostController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
	'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'create', 'update', 'delete', 'view'],
                        'allow' => true,
                        'roles' => ['Author', 'Admin'],
                    ],
                ],
            ],

            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Post models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Post::find()->orderby('date DESC'),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Post model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Post model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
{
    $model = new Post();

    if ($model->load(Yii::$app->request->post())) {
        // Jika berhasil disimpan
        if ($model->save()) {
            Yii::$app->session->setFlash('success', 'Artikel berhasil dibuat!');
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            // Jika gagal disimpan (misal: validasi gagal atau error database)
            Yii::$app->session->setFlash('error', 'Gagal membuat artikel. Silakan periksa kembali isian Anda.');
        }
    }

    return $this->render('create', [
        'model' => $model,
    ]);
}

    /**
     * Updates an existing Post model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
{
    $model = $this->findModel($id);

    if ($model->load(Yii::$app->request->post())) {
        if ($model->save()) {
            Yii::$app->session->setFlash('success', 'Artikel berhasil diperbarui!');
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            Yii::$app->session->setFlash('error', 'Gagal memperbarui artikel.');
        }
    }

    return $this->render('update', [
        'model' => $model,
    ]);
}
    /**
     * Deletes an existing Post model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        try {
            $this->findModel($id)->delete();
            Yii::$app->session->setFlash('success', 'Artikel berhasil dihapus!');
        } catch (\Exception $e) {
            // Tangkap error jika artikel tidak bisa dihapus (misal: berelasi dengan data lain)
            Yii::$app->session->setFlash('error', 'Gagal menghapus artikel: ' . $e->getMessage());
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the Post model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Post the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Post::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
