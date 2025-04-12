install xwampp and start apache and mysql

#register 
create xampp db sql :
-- Create the database
CREATE DATABASE IF NOT EXISTS user_auth;

-- Select the database
USE user_auth;

-- Create the 'users' table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- Create the 'otp_table' for storing OTPs
CREATE TABLE IF NOT EXISTS otp_table (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    otp VARCHAR(10) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


# then run app.py 
cd path of folder 
python app.py

then http://localhost/Login%20and%20Signup%20Form%20Design/
