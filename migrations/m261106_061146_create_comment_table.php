<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%comment}}`.
 */
class m261106_061146_create_comment_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
{
    $this->createTable('comment', [
        'id' => $this->primaryKey(),
        'post_id' => $this->integer()->notNull(),
        'username' => $this->string(255)->notNull(),
        'content' => $this->text()->notNull(),
        'created_at' => $this->dateTime()->notNull(),
    ]);

    // Relasi foreign key ke tabel post
    $this->addForeignKey('fk-comment-post_id', 'comment', 'post_id', 'post', 'idpost', 'CASCADE', 'CASCADE');
}

public function down()
{
    $this->dropTable('comment');
}
}
