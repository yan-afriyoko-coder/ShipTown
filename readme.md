## About

YouTube Training Videos: 
- https://www.youtube.com/channel/UCl04S5dRXop1ZdZsOqY3OnA/featured

Black Box Test:
- https://app.process.st/templates/Products-Management-Black-Box-Testing-piNo-e4Q4PIKjZG89k5Ksg/view

Deployment Checklist:


## Setup
* `composer install`
* `npm install`


### Run seeder
```
php artisan db:seed
```
This will run the seeders which should populate necessary tables, including generating one user which will be given the 'admin' role.

## Modules
You will find all modules in separate folders.

app/Modules/*

Each module has separate folder and need to be decoupled

All modules load trough App service provider
https://github.com/ArturHanusek/management.products.api/blob/dev/config/app.php#L200

Each time ServiceProvider is booted, module checks against database if it should be enabled.

You can configure modules in _UI > Settings > Modules_

