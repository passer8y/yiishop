<?php

use yii\db\Migration;

class m170724_033121_alter_column_login_to_user_table extends Migration
{
    public function safeUp()
    {

    }

    public function safeDown()
    {
        echo "m170724_033121_alter_column_login_to_user_table cannot be reverted.\n";

        return false;
    }


    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->alterColumn('user','last_login_ip','string(50)');
    }

    public function down()
    {
        echo "m170724_033121_alter_column_login_to_user_table cannot be reverted.\n";

        return false;
    }

}
