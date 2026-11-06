<?php

namespace app\controllers;

use Yii;
use app\models\Post;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\UploadedFile;

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
            
            // Logika Upload Thumbnail
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
            if ($model->imageFile) {
                // Buat nama file unik
                $filename = time() . '_' . $model->imageFile->baseName . '.' . $model->imageFile->extension;
                // Simpan ke folder web/uploads
                $model->imageFile->saveAs('uploads/' . $filename);
                $model->thumbnail = $filename;
            }

            if ($model->save(false)) { // false agar validasi imageFile tidak memblokir simpan
                Yii::$app->session->setFlash('success', 'Artikel berhasil dibuat!');
                return $this->redirect(['index']);
            }
        }

        return $this->render('create', ['model' => $model]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $oldThumbnail = $model->thumbnail; // Simpan nama file lama

        if ($model->load(Yii::$app->request->post())) {
            
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
            if ($model->imageFile) {
                $filename = time() . '_' . $model->imageFile->baseName . '.' . $model->imageFile->extension;
                $model->imageFile->saveAs('uploads/' . $filename);
                $model->thumbnail = $filename;
            } else {
                $model->thumbnail = $oldThumbnail; // Gunakan yang lama jika tidak ada upload baru
            }

            if ($model->save(false)) {
                Yii::$app->session->setFlash('success', 'Artikel berhasil diperbarui!');
                return $this->redirect(['index']);
            }
        }

        return $this->render('update', ['model' => $model]);
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
