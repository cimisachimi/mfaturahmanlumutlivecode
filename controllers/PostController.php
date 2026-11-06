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
     * Fungsi helper terpusat untuk mendeteksi apakah user yang login adalah Admin
     */
    private function isAdminUser()
    {
        $identity = Yii::$app->user->identity;
        if (!$identity) {
            return false;
        }
        $username = strtolower($identity->username ?? '');
        $role = strtolower($identity->role ?? '');

        // Validasi true jika role adalah admin ATAU username mengandung 'adm'/'admin'
        return ($role === 'admin' || $username === 'admin' || strpos($username, 'adm') === 0);
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        // Aksi yang boleh dilihat oleh guest (belum login) dan user login
                        'actions' => ['view'],
                        'allow' => true,
                        'roles' => ['?', '@'], 
                    ],
                    [
                        // Tambahkan aksi trash, restore, dan force-delete di sini
                        'actions' => ['index', 'create', 'update', 'delete', 'submit-review', 'approval', 'process-approval', 'delete-comment', 'report-comment', 'trash', 'restore', 'force-delete'],
                        'allow' => true,
                        'roles' => ['@'], 
                    ],
                ],
            ],
            'verbs' => [
                'class' => \yii\filters\VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                    'process-approval' => ['POST'],
                    'delete-comment' => ['POST'],
                    'report-comment' => ['POST'],
                    'restore' => ['POST'],
                    'force-delete' => ['POST'],
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
        $query = \app\models\Post::find()->where(['<>', 'status', 'deleted']); // Sembunyikan artikel berstatus deleted dari index utama
        $username = Yii::$app->user->identity->username;

        // Gunakan helper isAdminUser()
        if (!$this->isAdminUser()) {
            $query->andWhere(['username' => $username]);
        }

        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'date' => SORT_DESC,
                ]
            ],
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
        $model = Post::findOne($id);
        if ($model === null || strtolower($model->status) === 'deleted') {
            throw new NotFoundHttpException('Artikel tidak ditemukan.');
        }

        $isGuest = Yii::$app->user->isGuest;
        $username = !$isGuest ? Yii::$app->user->identity->username : null;
        $isAdmin = $this->isAdminUser();

        // 1. Jika pembaca adalah Guest (belum login), hanya boleh melihat artikel yang sudah Approved
        if ($isGuest) {
            if (strtolower($model->status) !== 'approved') {
                throw new NotFoundHttpException('Artikel tidak ditemukan atau belum dipublikasikan.');
            }
        } else {
            // 2. Jika sudah login, admin boleh lihat semua. Author hanya boleh lihat miliknya atau yang sudah approved
            if (!$isAdmin && $model->username !== $username && strtolower($model->status) !== 'approved') {
                throw new \yii\web\ForbiddenHttpException('Anda tidak berhak mengakses artikel ini.');
            }
        }

        // Tangani proses Review Admin langsung dari halaman view (jika admin)
        if ($isAdmin && Yii::$app->request->isPost && Yii::$app->request->post('action')) {
            $action = Yii::$app->request->post('action');
            $note = Yii::$app->request->post('admin_note');

            if ($action === 'approve') {
                $model->status = 'approved';
                $model->reviewed_at = date('Y-m-d H:i:s');
                $model->admin_note = null;
                $model->save(false);
                Yii::$app->session->setFlash('success', 'Artikel berhasil disetujui (Approved).');
            } elseif ($action === 'reject') {
                $model->status = 'reject';
                $model->reviewed_at = date('Y-m-d H:i:s');
                $model->admin_note = !empty($note) ? $note : 'Mohon lakukan revisi pada konten artikel Anda.';
                $model->save(false);
                Yii::$app->session->setFlash('success', 'Artikel ditolak dan catatan revisi dikirim ke author.');
            }
            return $this->refresh();
        }

        // Tangani Kirim Komentar Baru
        $comment = new \app\models\Comment();
        if ($comment->load(Yii::$app->request->post())) {
            if ($isGuest) {
                Yii::$app->session->setFlash('error', 'Anda harus login terlebih dahulu untuk memberikan komentar.');
                return $this->refresh();
            }

            $comment->post_id = $model->idpost;
            $comment->username = $username;
            $comment->created_at = date('Y-m-d H:i:s');

            if ($comment->save()) {
                Yii::$app->session->setFlash('success', 'Komentar berhasil dikirim.');
                return $this->refresh();
            }
        }

        return $this->render('view', [
            'model' => $model,
            'commentModel' => $comment,
        ]);
    }

    /**
     * Creates a new Post model.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Post();

        if ($model->load(Yii::$app->request->post())) {
            
            $model->username = Yii::$app->user->identity->username;
            $model->date = date('Y-m-d H:i:s');
            
            $isAdmin = $this->isAdminUser();

            if (!$isAdmin) {
                if (!in_array($model->status, ['draft', 'pending'])) {
                    $model->status = 'draft';
                }
                $model->admin_note = null;
            } else {
                if (empty($model->status)) {
                    $model->status = 'approved';
                }
            }

            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
            if ($model->imageFile) {
                $filename = time() . '_' . $model->imageFile->baseName . '.' . $model->imageFile->extension;
                $uploadPath = 'uploads/' . $filename;
                
                if ($model->imageFile->saveAs($uploadPath)) {
                    $model->thumbnail = $filename;
                }
            }

            try {
                if ($model->save(false)) {
                    Yii::$app->session->setFlash('success', 'Artikel berhasil dibuat!');
                    return $this->redirect(['index']);
                } else {
                    Yii::$app->session->setFlash('error', 'Gagal menyimpan data artikel.');
                }
            } catch (\Exception $e) {
                Yii::$app->session->setFlash('error', 'Database Exception: ' . $e->getMessage());
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $oldThumbnail = $model->thumbnail; 

        if ($model->load(Yii::$app->request->post())) {
            
            $isAdmin = $this->isAdminUser();

            if (!$isAdmin) {
                if (!in_array($model->status, ['draft', 'pending'])) {
                    $model->status = 'draft';
                }
                $model->admin_note = $model->oldAttributes['admin_note'] ?? null; 
            } else {
                if (in_array($model->status, ['approved', 'reject'])) {
                    $model->reviewed_at = date('Y-m-d H:i:s');
                }
            }

            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
            if ($model->imageFile) {
                $filename = time() . '_' . $model->imageFile->baseName . '.' . $model->imageFile->extension;
                $uploadPath = 'uploads/' . $filename;
                
                if ($model->imageFile->saveAs($uploadPath)) {
                    $model->thumbnail = $filename;
                }
            } else {
                $model->thumbnail = $oldThumbnail; 
            }

            try {
                if ($model->save(false)) {
                    Yii::$app->session->setFlash('success', 'Artikel berhasil diperbarui!');
                    return $this->redirect(['index']);
                } else {
                    Yii::$app->session->setFlash('error', 'Gagal memperbarui artikel.');
                }
            } catch (\Exception $e) {
                Yii::$app->session->setFlash('error', 'Database Exception: ' . $e->getMessage());
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Soft Deletes an existing Post model (Pindah ke Riwayat Hapus).
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $username = Yii::$app->user->identity->username;
        $isAdmin = $this->isAdminUser();

        if ($isAdmin || $model->username === $username) {
            $model->status = 'deleted'; // Soft delete
            $model->save(false);
            Yii::$app->session->setFlash('success', 'Artikel berhasil dipindahkan ke Riwayat Hapus.');
        } else {
            throw new \yii\web\ForbiddenHttpException('Anda tidak berhak menghapus artikel ini.');
        }

        return $this->redirect(['index']);
    }

    /**
     * Menampilkan daftar artikel di Riwayat Hapus (Khusus Admin).
     */
    public function actionTrash()
    {
        if (!$this->isAdminUser()) {
            throw new \yii\web\ForbiddenHttpException('Akses Ditolak. Hanya Admin yang dapat melihat Riwayat Hapus.');
        }

        $query = \app\models\Post::find()->where(['status' => 'deleted']);
        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'date' => SORT_DESC,
                ]
            ],
        ]);

        return $this->render('trash', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Mengembalikan artikel dari Riwayat Hapus ke status Pending (Khusus Admin).
     */
    public function actionRestore($id)
    {
        if (!$this->isAdminUser()) {
            throw new \yii\web\ForbiddenHttpException('Akses ditolak.');
        }

        $model = $this->findModel($id);
        $model->status = 'pending';
        $model->save(false);
        Yii::$app->session->setFlash('success', 'Artikel berhasil dikembalikan ke status Pending.');
        return $this->redirect(['trash']);
    }

    /**
     * Menghapus artikel secara permanen dari database (Khusus Admin).
     */
    public function actionForceDelete($id)
    {
        if (!$this->isAdminUser()) {
            throw new \yii\web\ForbiddenHttpException('Akses ditolak.');
        }

        $model = $this->findModel($id);
        try {
            $model->delete();
            Yii::$app->session->setFlash('success', 'Artikel berhasil dihapus secara permanen.');
        } catch (\Exception $e) {
            Yii::$app->session->setFlash('error', 'Gagal menghapus permanen: ' . $e->getMessage());
        }

        return $this->redirect(['trash']);
    }

    public function actionSubmitReview($id)
    {
        $model = $this->findModel($id);
        $username = Yii::$app->user->identity->username;
        $isAdmin = $this->isAdminUser();

        if ($isAdmin || $model->username === $username) {
            if (in_array($model->status, ['draft', 'reject'])) {
                $model->status = 'pending';
                $model->submitted_at = date('Y-m-d H:i:s');
                
                if ($model->status === 'reject' || !empty($model->admin_note)) {
                    $model->revision_count += 1;
                }

                $model->admin_note = null; 
                $model->save(false);
                Yii::$app->session->setFlash('success', 'Artikel berhasil disubmit untuk review.');
            }
        }
        return $this->redirect(['index']);
    }

    public function actionApproval()
    {
        if (!$this->isAdminUser()) {
            throw new \yii\web\ForbiddenHttpException('Anda tidak memiliki hak akses ke halaman Approval.');
        }

        // Query untuk Post yang pending
        $postQuery = \app\models\Post::find()->where(['status' => 'pending'])->orderBy(['submitted_at' => SORT_DESC]);
        $postDataProvider = new \yii\data\ActiveDataProvider([
            'query' => $postQuery,
            'pagination' => ['pageSize' => 10],
        ]);

        // Query khusus untuk Komentar yang dilaporkan (is_reported = true)
        $commentQuery = \app\models\Comment::find()->where(['is_reported' => true])->orderBy(['created_at' => SORT_DESC]);
        $commentDataProvider = new \yii\data\ActiveDataProvider([
            'query' => $commentQuery,
            'pagination' => ['pageSize' => 10],
        ]);

        return $this->render('approval', [
            'dataProvider' => $postDataProvider,
            'commentDataProvider' => $commentDataProvider,
        ]);
    }

    public function actionProcessApproval($id)
    {
        if (!$this->isAdminUser()) {
            throw new \yii\web\ForbiddenHttpException('Aksi ditolak.');
        }

        $model = $this->findModel($id);
        $action = Yii::$app->request->post('action'); 
        $note = Yii::$app->request->post('admin_note'); 

        if ($action === 'approve') {
            $model->status = 'approved';
            $model->reviewed_at = date('Y-m-d H:i:s');
            $model->admin_note = null;
            $model->save(false);
            Yii::$app->session->setFlash('success', 'Artikel "' . $model->title . '" berhasil disetujui (Approved).');
        } elseif ($action === 'reject') {
            $model->status = 'reject';
            $model->reviewed_at = date('Y-m-d H:i:s');
            $model->admin_note = !empty($note) ? $note : 'Mohon lakukan revisi pada konten artikel Anda.';
            $model->save(false);
            Yii::$app->session->setFlash('success', 'Artikel "' . $model->title . '" ditolak dan catatan revisi telah dikirim ke author.');
        }

        return $this->redirect(['approval']);
    }

    public function actionDeleteComment($id)
    {
        $comment = \app\models\Comment::findOne($id);
        if (!$comment) {
            throw new \yii\web\NotFoundHttpException('Komentar tidak ditemukan.');
        }

        $post = $comment->post;
        $username = Yii::$app->user->identity->username;
        $isAdmin = $this->isAdminUser();

        // Hak hapus: Admin, Pemilik Artikel (Author), atau Pemilik Komentar itu sendiri
        if ($isAdmin || $post->username === $username || $comment->username === $username) {
            $postId = $comment->post_id;
            $comment->delete();
            Yii::$app->session->setFlash('success', 'Komentar berhasil dihapus.');
            return $this->redirect(['view', 'id' => $postId]);
        }

        throw new \yii\web\ForbiddenHttpException('Anda tidak berhak menghapus komentar ini.');
    }

    public function actionReportComment($id)
    {
        $comment = \app\models\Comment::findOne($id);
        if (!$comment) {
            throw new \yii\web\NotFoundHttpException('Komentar tidak ditemukan.');
        }

        $comment->is_reported = true;
        $comment->save(false);

        Yii::$app->session->setFlash('success', 'Komentar berhasil dilaporkan ke Admin.');
        return $this->redirect(['post/view', 'id' => $comment->post_id]);
    }

    /**
     * Finds the Post model based on its primary key value.
     * @param integer $id
     * @return Post the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = \app\models\Post::findOne($id)) !== null) {
            
            $username = Yii::$app->user->identity->username;
            $isAdmin = $this->isAdminUser();

            // Jika bukan admin, pastikan hanya bisa akses artikel miliknya sendiri
            if (!$isAdmin && $model->username !== $username) {
                throw new \yii\web\ForbiddenHttpException('Anda tidak berhak mengakses atau mengedit artikel milik orang lain.');
            }
            
            return $model;
        }

        throw new \yii\web\NotFoundHttpException('The requested page does not exist.');
    }
}