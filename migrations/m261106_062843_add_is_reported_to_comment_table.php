<?php

use yii\db\Migration;

class m261106_062843_add_is_reported_to_comment_table extends Migration
{
    /**
     * {@inheritdoc}
     */
public function up()
{
    $this->addColumn('comment', 'is_reported', $this->boolean()->defaultValue(false));
}

public function down()
{
    $this->dropColumn('comment', 'is_reported');
}

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m261106_062843_add_is_reported_to_comment_table cannot be reverted.\n";

        return false;
    }
    */
}
