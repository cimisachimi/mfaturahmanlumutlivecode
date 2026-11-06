<?php

use yii\db\Migration;

class m261106_054644_add_tracking_columns_to_post extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('post', 'submitted_at', $this->dateTime()->defaultValue(null));
        $this->addColumn('post', 'reviewed_at', $this->dateTime()->defaultValue(null));
        $this->addColumn('post', 'revision_count', $this->integer()->defaultValue(0));
    }
    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('post', 'submitted_at');
        $this->dropColumn('post', 'reviewed_at');
        $this->dropColumn('post', 'revision_count');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m261106_054644_add_tracking_columns_to_post cannot be reverted.\n";

        return false;
    }
    */
}
