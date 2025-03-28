# Documentació

## Crear la nova DB y fer el migrate
 * Crear la bd
   * docker-compose exec db mysql -u root -proot -e "CREATE DATABASE cinema_testing;"
 * Verificar que la base de datos existe
   * docker-compose exec db mysql -u root -proot -e "SHOW DATABASES;"
 * Asegurar permisos del usuario root para conexiones remotas (desde el contenedor Laravel)
   * docker-compose exec db mysql -u root -proot -e "ALTER USER 'root'@'%' IDENTIFIED WITH mysql_native_password BY 'root'; FLUSH PRIVILEGES;"
 * Migración
   * docker-compose exec laravel bash -c "php artisan migrate:fresh --seed --env=testing"
 * Tests
   * docker-compose exec laravel bash -c "php artisan test"