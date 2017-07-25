<?php

use yii\db\Migration;

class m170724_032231_add_column_login_to_user_table extends Migration
{
    public function safeUp()
    {

    }

    public function safeDown()
    {
        echo "m170724_032231_add_column_login_to_user_table cannot be reverted.\n";

        return false;
    }


    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->addColumn('user','last_login_time','integer');
        $this->addColumn('user','last_login_ip','integer');
    }

    public function down()
    {
        echo "m170724_032231_add_column_login_to_user_table cannot be reverted.\n";

        return false;
    }

}
