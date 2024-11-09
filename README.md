# Car Rental System

A simple car rental system built with HTML, CSS, and PHP.

## Database Setup

1. Open your PHPMyAdmin (through Laragon)
2. Create a new database named `car_rental_db`
3. Click on the newly created database
4. Click on "Import" tab
5. Upload the `database.sql` file provided
6. Click "Go" to import the database structure

## Project Setup

1. Clone this repository to your Laragon's www directory:
   ```
   C:/laragon/www/car-rental-system
   ```
2. Start Laragon
3. Access the project through:
   ```
   http://car-rental-system.test
   ```

## Database Structure

The system uses the following tables:
- cars: Stores car models and their details
- bookings: Stores rental bookings
- admin: Stores admin credentials

## Features

- Car Browsing & Booking for Clients
- Admin Dashboard for Car Management
- Booking Management
- No Registration Required for Clients
- Reference Number Based Booking Tracking 

- if the admin account is not working Upload the create_admin.php file to your admin folder
Access it through your browser (e.g., http://your-site/admin/create_admin.php)
This will create a new admin account with:
Username: admin
Password: admin123
Delete the create_admin.php file after using it

- mag kaka error sa 'Book now' rent car pag may error add this query sa database "ALTER TABLE bookings MODIFY COLUMN reference_no VARCHAR(20) NOT NULL;"  punta lang sa MyPhpAdmin then click the car_rental_db then click SQL tapos paste the query. 
