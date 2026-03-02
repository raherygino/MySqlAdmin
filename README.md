# SGBD – Web-Based Database Management System

A lightweight, modern web-based MySQL database manager built with PHP and PDO.  
Similar to phpMyAdmin, but with a clean, minimal interface.

## Features

- **Authentication** – Login with MySQL credentials; session-based auth
- **Database Management** – Create, rename, drop databases
- **Table Management** – Create, drop, truncate tables; view structure
- **Column Management** – Add, edit (rename/retype), drop columns
- **Record Management** – Browse (paginated, sortable, searchable), insert, edit, delete rows
- **SQL Editor** – Execute arbitrary SQL with syntax shortcuts and Ctrl+Enter
- **Import** – Upload and execute `.sql` files (up to 50 MB)
- **Export** – Download databases/tables as `.sql` dumps
- **Security** – PDO prepared statements prevent SQL injection; HTML output escaped to prevent XSS
- **Responsive UI** – Modern CSS with Lucide icons; works on desktop and mobile

## Requirements

- PHP 7.4+ (with PDO and PDO_MySQL extensions)
- MySQL 5.7+ or MariaDB 10.3+
- Apache with `mod_rewrite` (XAMPP recommended)

## Installation

1. Clone or copy this folder into your web server root:
   ```
   c:\xampp\htdocs\SGBD\
   ```
2. Start Apache and MySQL from the XAMPP Control Panel.
3. Open your browser and navigate to:
   ```
   http://localhost/SGBD/
   ```
4. Log in with your MySQL credentials (default: `root` / empty password).