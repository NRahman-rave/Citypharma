# CityPharma — Pharmacy Management System

A web-based pharmacy management system developed as an individual university project using PHP and MySQL.

CityPharma helps manage pharmacy operations, including medicine inventory, suppliers, prescriptions, purchase orders, dispensing and payments.

## Features

- **Role-based authentication:** Separate manager and staff dashboards.
- **Medicine management:** Add, edit, view and delete medicines.
- **Inventory tracking:** Manage medicine batches, quantities and expiry dates.
- **Supplier management:** Organise supplier information and purchase orders.
- **Prescription management:** Create and manage prescriptions.
- **Medicine dispensing:** Record dispensing operations.
- **Payments:** Manage pharmacy payment records.
- **Audit logs:** Track selected database operations.
- **Reporting:** View operational summaries.

## Technologies Used

- PHP
- MySQL / MariaDB
- HTML5
- CSS3
- JavaScript
- Bootstrap
- XAMPP (Apache and MySQL)

## Security Improvements

The project includes security improvements such as:

- Password hash verification using `password_verify()`.
- Prepared SQL statements for login and medicine-management operations.
- Session regeneration following successful authentication.
- CSRF protection for medicine modification forms.
- Manager-only access checks for medicine administration.
- Private database credentials stored outside version control.

These protections are implemented in the updated components and do not constitute a complete production security audit.

## Installation

1. Install XAMPP with PHP and MySQL/MariaDB.
2. Download or clone this repository.
3. Place the project in `C:\xampp\htdocs\CityPharma`.
4. Start Apache and MySQL using the XAMPP Control Panel.
5. Open phpMyAdmin and create a database named `citypharma_db`.
6. Import `database_schema.sql` into the new database.
7. Copy `config.local.example.php` and rename the copy to `config.local.php`.
8. Edit `config.local.php` with your local database credentials. Use a dedicated database account with appropriate permissions.
9. Open `http://localhost/CityPharma/login.php`.

**Account setup:** The database schema contains no user accounts. An initial manager or staff account must be created locally using a securely generated password hash from PHP's `password_hash()` function. No default login credentials are included.

## Database

The database schema includes 13 interconnected tables, including:

- Users
- Medicines
- Batches
- Suppliers
- Prescriptions and prescription items
- Purchase orders and purchase order items
- Dispensing
- Payments
- Sales
- Stock adjustments
- Audit logs

The `database_schema.sql` file contains table definitions, indexes and foreign-key relationships without real pharmacy or patient records.

## Project Background

CityPharma was developed independently as part of my BSc Computer Science studies. The project helped me gain practical experience with relational database design, PHP backend development, authentication, CRUD operations and application security.

**Note:** This is an educational project, not a production-certified pharmacy system.

## Developer

**Nayem Rahman**

- GitHub: https://github.com/NRahman-rave
- Portfolio: https://nayemr.netlify.app
