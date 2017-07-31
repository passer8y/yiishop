<?php

use yii\db\Migration;

/**
 * Handles the creation of table `address`.
 */
class m170730_021436_create_address_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('address', [
            'id' => $this->primaryKey(),
            'name' => $this->string(30)->comment('收货人'),
            'area' => $this->string(255)->comment('所在地区'),
            'address' => $this->string(255)->comment('详细地址'),
            'tel' => $this->string(11)->comment('手机号码'),
            'status' => $this->integer(1)->comment('默认地址 1:默认,0:正常'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('address');
    }
}
