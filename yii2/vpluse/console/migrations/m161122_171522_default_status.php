<?php

use yii\db\Migration;

class m161122_171522_default_status extends Migration
{
    public function up()
    {
        $this->execute("
          ALTER TABLE `user_request` CHANGE `status` `status` ENUM( '1', '2', '3' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '1' COMMENT '1-Ожидание, 2-Подтвержден, 3-Отклонен'; ;
        ");
    }

    public function down()
    {
        $this->execute("
          ALTER TABLE `user_request` CHANGE `status` `status` ENUM( '1', '2', '3' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '1-Ожидание, 2-Подтвержден, 3-Отклонен';
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
