# Sistema de Gestión de Inventarios para Perfumería

Este proyecto es un sistema de gestión de inventarios para perfumerías desarrollado con Laravel (backend) y Vue.js (frontend). Controla entradas, salidas y stock por ubicación.

## Características

- Gestión de usuarios con niveles de permisos
- Catálogo de productos
- Ubicaciones de almacenamiento
- Control de stock por producto y ubicación
- Registro de entradas y salidas de inventario
- API RESTful para integración
- Interfaz de usuario con Vue.js

## Instalación

1. Clona el repositorio
2. Instala las dependencias de PHP: `composer install`
3. Instala las dependencias de Node.js: `npm install`
4. Configura la base de datos en `.env`
5. Ejecuta las migraciones: `php artisan migrate`
6. Ejecuta los seeders: `php artisan db:seed`
7. Compila los assets: `npm run build`
8. Inicia el servidor: `php artisan serve`

## Uso

- Accede a la aplicación en `http://localhost:8000`
- Usa la navegación para gestionar usuarios, productos, ubicaciones, stock, entradas y salidas
- Las APIs están disponibles en `/api/*`

## Estructura de la Base de Datos

- `ct_usuarios`: Usuarios del sistema
- `ct_productos`: Productos disponibles
- `ct_ubicaciones`: Ubicaciones de almacenamiento
- `ct_stock`: Stock actual por producto y ubicación
- `ct_entradas` y `ct_detalle_entrada`: Registros de entradas
- `ct_salidas` y `ct_detalle_salidas`: Registros de salidas

## Tecnologías

- Laravel 11
- Vue.js 3
- MySQL
- Tailwind CSS
- Axios

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
