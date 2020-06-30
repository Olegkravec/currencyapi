# Laravel API Application

## How to build

### Requirements
* Redis
* MySQL, prefer >8.0
* node.js

### Installing
* Configure ***.env*** file regarding to ***.env.example***
* composer update
* php artisan db:seed (will add 50 users and super-admin)
* ***(if needed)*** laravel-echo-server init 
* Check if 'laravel-echo-server.json' contains valid ***authHost*** directive. ***Cannot be ip or localhost***
* Run your web-server with DocumentRoot insde of .../public directory
* laravel-echo-server start
* Open application in browser

### Using

#### Default auth data

SuperAdmin default:

* login: iamadmin@gmail.com 
* password: password

50 other's users passwords:
* password

