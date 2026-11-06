<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "post".
 *
 * @property int $idpost
 * @property string $title
 * @property string $content
 * @property string $date
 * @property string $username
 *
 * @property Account $username0
 */
class Post extends \yii\db\ActiveRecord
{
    public $imageFile;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'post';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'content', 'date', 'username'], 'required'],
            [['title', 'content'], 'string'],
            [['date'], 'safe'],
            [['username'], 'string', 'max' => 45],
            [['username'], 'exist', 'skipOnError' => true, 'targetClass' => Account::className(), 'targetAttribute' => ['username' => 'username']],
            [['status'], 'string', 'max' => 20],
            [['status'], 'in', 'range' => ['draft', 'pending', 'approved', 'reject']],
            [['thumbnail'], 'string', 'max' => 255],
            [['imageFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg'],
            

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idpost' => 'Idpost',
            'title' => 'Title',
            'content' => 'Content',
            'date' => 'Date',
            'username' => 'Username',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsername0()
    {
        return $this->hasOne(Account::className(), ['username' => 'username']);
    }

    public static function getStatusList()
    {
        return [
            'draft' => 'Draft',
            'pending' => 'Pending',
            'approved' => 'Approved',
            'reject' => 'Reject'
        ];
    }
}
