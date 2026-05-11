
## Wi De Ya Laravel Web App

This web application interfaces with the [Wi De Ya Android App](https://github.com/CGATechnologies/wideya-android) 
and is used for user management, data management and synchronisation with mobile app, and data analysis dashboards.
The primary users of the web app are administrators, central staff, district staff, and all stakeholders interested 
in Sierra Leone education data.
Some dashboards are public, while others contain information only visible to permissioned users.

The Wi De Ya Android app is for Sierra Leone school management and attendance monitoring, with the primary user intended
to be the school leader (or administrative head) of each school. The app aims to help school leaders capture and manage 
information on classes, teachers, learners and their guardians and report daily attendance at the school, 
using biometrics for teachers (fingerprint matching and photos).

This Wi De Ya web app is written in PHP, leveraging Laravel framework version 9 (https://laravel.com/docs/9.x/)

## Setup for Development Environment

Requirements:
- PHP >=8.1
- MYSQL >=5.7 (ideally 8) or MariaDB equivalent
  - Install from https://dev.mysql.com/downloads/mysql/ or docker 
- Composer
- Node and NPM for asset compilation

To install:
1. Clone this repo
2. Create .env file from .env.example <code> cp .env.example .env</code>
3. Create a database for the app, i.e. name=wideya, character set=utf8mb4, collation=utf8mb4_unicode_ci
3. Enter local env params, most importantly database connection details with database above
4. Run migrations using command <code> php artisan migrate</code> 
5. Run seeding using command <code>php artisan db:seed</code>
6. Run composer to install PHP dependencies <code>composer install</code>
7. Optional: Run npm to install JS asset dependencies <code>npm install</code>

To run:
- Launch the app using <code>php artisan serve</code>
- Optional: Open another terminal and run vite for hot reloading <code>npm run dev</code>

## Licenses

License to use and redevelop this code are reserved to Teaching Service Commission Sierra Leone and CGA Technologies only.

Various existing packages are leveraged in this Wi De Ya Web App:

* Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
* [Datatables Editor](https://packagist.org/packages/datatables.net/editor-php) is commercial software. See [license details](https://editor.datatables.net/license)
* Highcharts is open-source commercial licensed software. See [license details](https://github.com/highcharts/highcharts/blob/master/license.txt)
