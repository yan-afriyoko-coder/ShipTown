## About
TBD

## Setup
* `composer install`
* `npm install`

## Adding default roles and permissions
```
php artisan permission:create-role admin web "manage users|invite users"
php artisan permission:create-role user web
```