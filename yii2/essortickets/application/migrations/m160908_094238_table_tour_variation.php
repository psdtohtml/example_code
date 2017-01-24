<?php

use yii\db\Migration;

/**
 * Create tour and variation tables
 * @author Alex Zakharov <alexzakharovwork@gmail.com>
 */
class m160908_094238_table_tour_variation extends Migration
{

    public function safeUp()
    {
        $this->execute("
        CREATE TABLE IF NOT EXISTS `tour` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Primary key',
            `manager_id` VARCHAR (55) DEFAULT NULL COMMENT 'Tour Manager Id.',
            `name` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Tour name',
            `description` VARCHAR (255) DEFAULT NULL COMMENT 'Short description of tour.',
            `recurring` VARCHAR (255) NULL DEFAULT NULL  COMMENT 'Recurring e.g. each week, month',
            `currency` VARCHAR(3) NULL DEFAULT NULL COMMENT 'Currency e.g USD GBR EUR',
            `ticket_available` INT UNSIGNED DEFAULT 0 COMMENT 'How many tickets have just for this tour',
            `ticket_booking_available` INT UNSIGNED DEFAULT 0 COMMENT 'How many tickets can be booked for this tour',
            `ticket_minimum` INT UNSIGNED DEFAULT 1 COMMENT 'The minimum amount of tickets to be sold',
            `time_zone` VARCHAR (50) DEFAULT NULL COMMENT 'Time zone of tour.',
            `start_tour_date` DATE COMMENT 'YYYY-MM-DD',
            `end_tour_date` DATE COMMENT 'YYYY-MM-DD',
            `start_tour_time` TIME COMMENT 'HH:MM:SS',
            `end_tour_time` TIME COMMENT 'HH:MM:SS',
            `total_customer` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Total amount ­ of customers that can book for the whole tour ­ e.g. 8',
            `customer_ticket_limit` INT UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Amount purchased ­ of tickets each customer  that can book in one time e.g 5',
            `notice` VARCHAR(255) NULL DEFAULT NULL COMMENT 'when the ticket goes off sale',
            PRIMARY KEY `pk_id` (`id`)
        ) COLLATE ='utf8_general_ci' ENGINE=InnoDB;
        ");

        $this->truncateTable('tour');

        $this->execute("
        CREATE TABLE IF NOT EXISTS `variation` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Primary key',
            `tour_id` INT UNSIGNED NOT NULL COMMENT 'Foreign key for table `tour`',
            `name` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Variation name',
            `date` DATE COMMENT 'Date start tour YYYY-MM-DD',
            `time` TIME COMMENT 'Time start tour HH:MM:SS',
            `total_customer` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Total amount ­ of customers that can book for the whole tour ­ e.g. 8',
            `customer_ticket_limit` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Amount purchased ­ of tickets each customer  that can book in one time e.g 5',
            `recurring_event` VARCHAR (255) NULL DEFAULT NULL  COMMENT '',
            `dates_of_recuring` DATE COMMENT 'Dates of recurring, select dates or every week, month YYYY-MM-DD',
            `adress_change` VARCHAR (255) NULL DEFAULT NULL  COMMENT 'For tickets update',
            `email_notifications` VARCHAR (255) NULL DEFAULT NULL  COMMENT 'Update email for notification',
            PRIMARY KEY `pk_id` (`id`),
            FOREIGN KEY `fk_tour_id` (`tour_id`) REFERENCES `tour`(`id`)
        ) COLLATE ='utf8_general_ci' ENGINE=InnoDB;
        ");

        $this->truncateTable('variation');
    }

    public function safeDown()
    {
        $this->dropTable('tour');
    }

}
