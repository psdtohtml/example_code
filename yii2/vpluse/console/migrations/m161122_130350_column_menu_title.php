<?php

use yii\db\Migration;

class m161122_130350_column_menu_title extends Migration
{
    public function up()
    {
        $this->execute("
          ALTER TABLE `section` ADD `menu_title` VARCHAR( 255 ) NOT NULL ;
        ");
    }

    public function down()
    {
        $this->execute("
          ALTER TABLE `section` DROP `menu_title`;
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
