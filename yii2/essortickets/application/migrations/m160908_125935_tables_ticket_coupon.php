<?php

use yii\db\Migration;

/**
 * Create ticket and coupon tables
 * @author Alex Zakharov <alexzakharovwork@gmail.com>
 */
class m160908_125935_tables_ticket_coupon extends Migration
{

    public function safeUp()
    {
        $this->execute("
        CREATE TABLE IF NOT EXISTS `ticket` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Primary key',
            `tour_id` INT UNSIGNED DEFAULT NULL COMMENT 'Foreign key for table tour',
            `availability_id` INT UNSIGNED DEFAULT NULL COMMENT 'Foreign key for id on table availability',
            `name` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Ticket name',
            `description` TEXT NULL DEFAULT NULL COMMENT 'Ticket description',
            `price` DECIMAL (10,2) NOT NULL COMMENT 'Ticket price',
            `booking_fee_type` INT UNSIGNED DEFAULT NULL COMMENT 'Percentage or fixed',
            `booking_fee` INT UNSIGNED DEFAULT NULL COMMENT 'Booking fee e.g. 5% or fixed 5USD',
            PRIMARY KEY `pk_id` (`id`),
            INDEX `i_tour_id` (`tour_id`),
            INDEX `i_availability_id` (`availability_id`)
        ) COLLATE ='utf8_general_ci' ENGINE=InnoDB;
        ");

        $this->truncateTable('ticket');

        $this->execute("
        CREATE TABLE IF NOT EXISTS `coupon` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Primary key',
            `ticket_id` INT UNSIGNED NOT NULL COMMENT 'Foreign key for table ticket',
            `name` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Coupon name',
            `code` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Coupon code',
            `limit` INT UNSIGNED DEFAULT NULL COMMENT 'Coupons limit',
            `starts_on` DATETIME COMMENT 'YYYY-MM-DD HH:MM:SS',
            `ends_on` DATETIME COMMENT 'YYYY-MM-DD HH:MM:SS',
            `valid_from` DATETIME COMMENT 'YYYY-MM-DD HH:MM:SS',
            `expires_on` DATETIME COMMENT 'YYYY-MM-DD HH:MM:SS',
            `discount_type` INT UNSIGNED DEFAULT NULL COMMENT 'Percentage or fixed',
            `discount` INT UNSIGNED DEFAULT NULL COMMENT 'Discount e.g. 5% or fixed 5USD',
            PRIMARY KEY `pk_id` (`id`),
            FOREIGN KEY `fk_ticket_id` (`ticket_id`) REFERENCES `ticket`(`id`)
        ) COLLATE ='utf8_general_ci' ENGINE=InnoDB;
        ");

        $this->truncateTable('coupon');
    }

    public function safeDown()
    {
        $this->dropTable('coupon');
        $this->dropTable('ticket');
    }

}
