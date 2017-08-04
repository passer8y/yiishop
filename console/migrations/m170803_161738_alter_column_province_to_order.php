<?php

use yii\db\Migration;

class m170803_161738_alter_column_province_to_order extends Migration
{
    /*public function safeUp()
    {

    }

    public function safeDown()
    {
        echo "m170803_161738_alter_column_province_to_order cannot be reverted.\n";

        return false;
    }*/


    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->alterColumn('order','province','string(255)');
    }

    public function down()
    {
        echo "m170803_161738_alter_column_province_to_order cannot be reverted.\n";

        return false;
    }

}
