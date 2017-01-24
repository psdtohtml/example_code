<?php

use yii\db\Migration;

/**
 * Create orders table
 * @author Alex Zakharov <alexzakharovwork@gmail.com>
 */
class m160908_143056_table_orders extends Migration
{

    public function safeUp()
    {
        $this->execute("
        CREATE TABLE IF NOT EXISTS `orders` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Primary key',
            `customer_id` INT UNSIGNED DEFAULT NULL COMMENT 'Foreign key for table `customer`',
            `user_id` INT UNSIGNED DEFAULT NULL COMMENT 'Foreign key for table `user`',
            `tax_id` INT UNSIGNED DEFAULT NULL COMMENT 'Foreign key for table `tax`',
            `status` INT DEFAULT 4 COMMENT 'e.g 1 = Confirmed, 2 = Expired, 3 = Cancelled, 4 = Pending, 5 = Provisional, 6 = Editing',
            `valid_from` DATETIME COMMENT 'YYYY-MM-DD HH:MM:SS',
            `valid_to` DATETIME COMMENT 'YYYY-MM-DD HH:MM:SS',
            `size` INT DEFAULT 1 COMMENT 'Size',
            `total_price` DECIMAL (10,2) DEFAULT NULL COMMENT 'Total Price',
            PRIMARY KEY `pk_id` (`id`),
            INDEX `i_customer_id` (`customer_id`),
            INDEX `i_user_id` (`user_id`),
            INDEX `i_tax_id` (`tax_id`)
        ) COLLATE ='utf8_general_ci' ENGINE=InnoDB;
        ");
    }

    public function safeDown()
    {
        $this->dropTable('orders');
    }

}

