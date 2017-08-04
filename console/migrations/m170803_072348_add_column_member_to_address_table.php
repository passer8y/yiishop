<?php

use yii\db\Migration;

class m170803_072348_add_column_member_to_address_table extends Migration
{
//    public function safeUp()
//    {
//
//    }

//    public function safeDown()
//    {
//        echo "m170803_072348_add_column_member_to_address_table cannot be reverted.\n";
//
//        return false;
//    }


    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->addColumn('address','member_id','integer');
    }

    public function down()
    {
        echo "m170803_072348_add_column_member_to_address_table cannot be reverted.\n";

        return false;
    }

}
