<?php

use yii\db\Migration;

class m170803_163317_alter_column_delivary_price_to_order extends Migration
{
    public function safeUp()
    {

    }

    public function safeDown()
    {
        echo "m170803_163317_alter_column_delivary_price_to_order cannot be reverted.\n";

        return false;
    }


    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->alterColumn('order','delivery_price','float(10,2)');
    }

    public function down()
    {
        echo "m170803_163317_alter_column_delivary_price_to_order cannot be reverted.\n";

        return false;
    }

}
