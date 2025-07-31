-- Database initialization script for STMS
-- This script runs when the MySQL container starts for the first time

-- Ensure the database exists
CREATE DATABASE IF NOT EXISTS `stms` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Ensure the user exists and has proper permissions
CREATE USER IF NOT EXISTS 'stms_user'@'%' IDENTIFIED BY 'stms_password';
GRANT ALL PRIVILEGES ON `stms`.* TO 'stms_user'@'%';
FLUSH PRIVILEGES;

-- Switch to the stms database
USE `stms`;

-- This file will be executed only on first container startup
-- Laravel migrations will handle table creation
