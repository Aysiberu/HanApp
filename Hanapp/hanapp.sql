-- Create the database and primary tables in a single idempotent script.
-- This file can be applied on a fresh server. If you already have
-- an existing database, double-check for conflicts before running.

-- For Supabase/Postgres: tables are created in your project's default database/schema.
-- Run this file first to create the `users` and `bookings` tables.

-- Users table (customers & providers, depending on registration flows)
CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    middle_name VARCHAR(100) DEFAULT '',
    last_name VARCHAR(100) NOT NULL,
    ext_name VARCHAR(10) DEFAULT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    mobile VARCHAR(25) DEFAULT NULL,
    house_number VARCHAR(50) DEFAULT NULL,
    street VARCHAR(255) DEFAULT NULL,
    location VARCHAR(255) DEFAULT NULL,
    zip VARCHAR(20) DEFAULT NULL,
    password VARCHAR(255) NOT NULL,
    reset_token VARCHAR(255) DEFAULT NULL,
    reset_expires TIMESTAMPTZ DEFAULT NULL,
    reset_code VARCHAR(6) DEFAULT NULL,
    reset_expiry TIMESTAMPTZ DEFAULT NULL,
    verify_email BOOLEAN NOT NULL DEFAULT true,
    created_at TIMESTAMPTZ DEFAULT now(),
    INDEX (`email`)
);

-- Bookings table — stores bookings made by users. provider_id can refer to providers.id where we use a separate providers table.
CREATE TABLE IF NOT EXISTS bookings (
    id SERIAL PRIMARY KEY,
    user_id INT NOT NULL,
    provider_id INT DEFAULT NULL,
    `provider_name` VARCHAR(255) DEFAULT NULL,
    `service_type` VARCHAR(255) DEFAULT NULL,
    `date_booked` DATE DEFAULT NULL,
    `location` VARCHAR(255) DEFAULT NULL,
    `status` VARCHAR(50) DEFAULT NULL,
    `contact` VARCHAR(50) DEFAULT NULL,
    `schedule` DATETIME DEFAULT NULL,
    `receipt_number` VARCHAR(100) DEFAULT NULL,
    `provider_photo` VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMPTZ DEFAULT now(),
    -- indexes
    -- Note: add foreign key to providers after providers table exists if needed
);


// fetch devices placeholder (in future, you can save devices to a table)
$devices = [
    ['model'=>'MSI','type'=>'Desktop','logged'=>'3:07 PM March 02 2023'],
    ['model'=>'Oppo','type'=>'Phone','logged'=>'10:21 AM February 09 2023'],
    ['model'=>'Acer','type'=>'Desktop','logged'=>'4:49 PM January 23 2021'],
];
       