-- Run these SQL commands directly in your MySQL database (phpMyAdmin, etc.)

-- Add missing columns to payments table
ALTER TABLE `payments` 
ADD COLUMN `payment_method` VARCHAR(255) NULL AFTER `amount`,
ADD COLUMN `reference_number` VARCHAR(255) NULL AFTER `payment_date`,
ADD COLUMN `notes` TEXT NULL AFTER `reference_number`;

-- Verify the table structure
DESCRIBE `payments`;
