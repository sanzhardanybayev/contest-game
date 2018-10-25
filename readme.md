
1. Run this command to install packages `docker run --rm -v ${pwd}:/app prooph/composer:7.1  install`
2. `docker-compose exec app php artisan key:generate`
3. `docker-compose exec app php artisan optimize`
4. `docker-compose exec app php artisa migrate` 
