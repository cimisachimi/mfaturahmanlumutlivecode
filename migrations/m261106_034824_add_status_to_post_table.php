<?php

use yii\db\Migration;

class m261106_034824_add_status_to_post_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('post', 'status', $this->string(20)->notNull()->defaultValue('draft'));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m261106_034824_add_status_to_post_table cannot be reverted.\n";
        $this->dropColumn('post', 'status');

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m261106_034824_add_status_to_post_table cannot be reverted.\n";

        return false;
    }
    */
}
