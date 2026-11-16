# WaterUse Hub - Water Usage Tracking and Billing System

A PHP, MySQL, HTML, CSS and JavaScript web application designed around the scope in the submitted Milestone One and Milestone Two documents.

## Requirements
- Windows with XAMPP (Apache, PHP 8+, MySQL/MariaDB)
- A modern web browser

## Installation
1. Extract the `wateruse_hub` folder into `C:\xampp\htdocs\`.
2. Open XAMPP Control Panel and start **Apache** and **MySQL**.
3. Visit `http://localhost/phpmyadmin`.
4. Choose **Import**, select `wateruse_hub/database/wateruse_hub.sql`, then click **Import**.
   - The SQL creates the `wateruse_hub` database and tables.
5. If your MySQL root account has a password, update `config/database.php`.
6. Visit `http://localhost/wateruse_hub/setup_admin.php` once to create the first administrator.
7. Immediately delete `setup_admin.php` from the project folder after it reports that the administrator was created.
8. Visit `http://localhost/wateruse_hub/` and sign in.

## First login
- Email: `admin@wateruse.local`
- Password: `Admin@123`

Change this password by creating a replacement administrator account and implementing a password-change workflow before any real deployment. This package is a development/demo baseline and is not hardened for public internet hosting.

## Suggested first-time workflow
1. Sign in as administrator.
2. Open **Settings** and enter the actual approved water tariff per m³. The initial value KES 100 is a sample placeholder only.
3. Open **Customers** and register customers.
4. Open **System users** and create staff accounts. To enable customer access, create a customer user linked to an existing customer.
5. Sign in as staff (or use admin role) and add meter readings for a billing period.
6. Generate a bill from an unbilled reading.
7. Record manual payments against the bill.
8. Use Complaints, Service actions, Notifications and Reports to test the other workflows.

## Important scope notes
- The application records payments manually. It does not integrate with M-PESA, banks or payment gateways.
- Meter readings are entered manually. There is no IoT or smart-meter integration.
- The tariff is configurable and must be set to the utility's approved rate before using actual data.
- Disconnection/reconnection actions are logged, but staff must follow the utility's policies and applicable notice requirements.
- Customer portal access is optional; staff can operate the system without creating customer logins.

## Security and deployment notes
This is an educational local-development package. Before production use:
- Add a secure password-change and password-reset workflow.
- Configure HTTPS, secure cookies, backups and server-level protections.
- Restrict database credentials and remove setup scripts.
- Add stronger audit review, rate limiting and operational authorization checks.
- Have the utility validate tariff rules, billing periods, notices and retention requirements.
- Test on a non-production database before importing real customer information.

## Troubleshooting
- **Database connection failed:** confirm MySQL is running and database credentials in `config/database.php` are correct.
- **404 Not Found:** check that the folder is exactly `C:\xampp\htdocs\wateruse_hub\`.
- **Apache/MySQL will not start:** resolve the port conflict in XAMPP before continuing.
- **Blank page/PHP error:** enable PHP error logging in XAMPP and inspect `xampp/php/logs/php_error_log`.

## Project structure
- `admin/` reserved for future dedicated admin pages; admin tools are in root pages and protected by role checks.
- `config/` database connection
- `includes/` shared functions and layout
- `database/` SQL schema
- `assets/` CSS and JavaScript
- Root PHP pages implement the application modules.
