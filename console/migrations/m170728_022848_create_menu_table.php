<?php

use yii\db\Migration;

/**
 * Handles the creation of table `menu`.
 */
class m170728_022848_create_menu_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('menu', [
            'id' => $this->primaryKey(),
            'name' => $this->string(50)->comment('菜单名称'),
            'url' => $this->string(30)->comment('地址/路由'),
            'parent_id' => $this->integer()->comment('上级分类ID'),
            'sort' => $this->integer()->comment('排序'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('menu');
    }
}
