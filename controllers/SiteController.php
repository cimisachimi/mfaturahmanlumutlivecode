<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
        {
            // Mengambil artikel yang berstatus 'approved', diurutkan dari yang terbaru
            $posts = \app\models\Post::find()
                ->where(['status' => 'approved'])
                ->orderBy(['date' => SORT_DESC])
                ->all();

            return $this->render('index', [
                'posts' => $posts,
            ]);
        }
    


    public function actionMobile(){
        return $this->render('mobile');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionSignup()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new \app\models\Account();
        
        // Ambil data POST secara mentah untuk menghindari blokir rules() bawaan Yii2
        $postData = Yii::$app->request->post('Account');

        if ($postData) {
            try {
                // 1. Assign nilai secara manual
                $model->username = $postData['username'];
                $model->name = !empty($postData['name']) ? $postData['name'] : $postData['username'];
                $model->role = 'Author';

                // 2. Hash Password
                if (!empty($postData['password'])) {
                    $model->password = Yii::$app->security->generatePasswordHash($postData['password']);
                }

                // 3. Simpan ke database (false = lewati validasi rules Yii, biarkan DB yang memvalidasi)
                if ($model->save(false)) {
                    Yii::$app->session->setFlash('success', 'Pendaftaran berhasil! Silakan login dengan akun baru Anda.');
                    return $this->redirect(['login']);
                }
                
            } catch (\yii\db\Exception $e) {
                // Menangkap Error #23000 dari Database
                if (isset($e->errorInfo[1]) && $e->errorInfo[1] == 1062) {
                    // Kode 1062 = Duplicate Entry
                    Yii::$app->session->setFlash('error', 'Pendaftaran gagal: Username tersebut sudah digunakan. Silakan pilih username lain.');
                } else {
                    // Error constraint lainnya (misal ada kolom lain yang wajib diisi di tabel Anda)
                    Yii::$app->session->setFlash('error', 'Pendaftaran gagal: Pastikan semua kolom terisi. (Detail: ' . $e->errorInfo[2] . ')');
                }
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }
    
}
