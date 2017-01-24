<?php

use yii\db\Migration;

class m161121_140837_add_subscribe_column extends Migration
{
    public function up()
    {
        $this->execute("
          ALTER TABLE `user` ADD `subscription` TINYINT( 1 ) NOT NULL DEFAULT '1' COMMENT '1-подписан. 0-неподписан';
        ");
    }

    public function down()
    {
        $this->execute("
          ALTER TABLE `user` DROP `subscription`;
        ");
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
