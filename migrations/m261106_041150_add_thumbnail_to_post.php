<?php

use yii\db\Migration;

class m261106_041150_add_thumbnail_to_post extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('post', 'thumbnail', $this->string(255)->defaultValue(null));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        
        $this->dropColumn('post', 'thumbnail');
        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m261106_041150_add_thumbnail_to_post cannot be reverted.\n";

        return false;
    }
    */
}
