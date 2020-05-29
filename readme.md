## About
TBD

## Setup
* `composer install`
* `npm install`

### Adding default roles and permissions
```
php artisan permission:create-role admin web "manage users|invite users"
php artisan permission:create-role user web
```

### Run seeder
```
php artisan db:seed
```
This will run the seeders which should populate necessary tables, including generating one user which will be given the 'admin' role.