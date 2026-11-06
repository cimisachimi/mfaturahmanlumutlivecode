<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "comment".
 *
 * @property int $id
 * @property int $post_id
 * @property string $username
 * @property string $content
 * @property string $created_at
 *
 * @property Post $post
 */
class Comment extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'comment';
    }

    public function rules()
    {
        return [
            [['post_id', 'username', 'content'], 'required'],
            [['post_id'], 'integer'],
            [['content'], 'string'],
            [['username'], 'string', 'max' => 255],
            [['post_id'], 'exist', 'skipOnError' => true, 'targetClass' => Post::className(), 'targetAttribute' => ['post_id' => 'idpost']],
        ];
    }

    public function getPost()
    {
        return $this->hasOne(Post::className(), ['idpost' => 'post_id']);
    }
}