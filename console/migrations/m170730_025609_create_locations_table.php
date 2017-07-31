<?php

use yii\db\Migration;

/**
 * Handles the creation of table `locations`.
 */
class m170730_025609_create_locations_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('locations', [
            'id' => $this->primaryKey(),
            //`name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
            'name' =>$this->string(255),
            //`parent_id` int(10) unsigned NOT NULL,
            'parent_id' =>$this->integer(10),
            //`level` tinyint(4) NOT NULL,
            'level' =>$this->integer(4),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('locations');
    }
}
