PROYECTO: logistica-aerea

Este archivo deja anotado SOLO lo minimo que se cambio para que el proyecto funcione en este entorno sin modificar su idea original.

1. BASE DE DATOS

- El proyecto ya estaba configurado en el archivo .env para usar:
  - Motor: MySQL / MariaDB
  - Host: 127.0.0.1
  - Puerto: 3306
  - Base de datos: logistica
  - Usuario: root
  - Password: vacio

- Se importo el respaldo SQL en la base:
  - logistica

- El archivo original entregado fue:
  - C:\xampp\htdocs\logica_aerea.sql

- Como el SQL fue exportado desde MySQL 8 y aqui el servidor activo es MariaDB 10.4, NO entraba directo por esta colacion:
  - utf8mb4_0900_ai_ci

- Para no tocar el respaldo original, se creo una copia compatible en:
  - database\logica_aerea_mariadb.sql

- A esa copia solo se le hicieron estos ajustes tecnicos de compatibilidad:
  - cambiar utf8mb4_0900_ai_ci por utf8mb4_unicode_ci
  - quitar la clausula DEFAULT ENCRYPTION='N'

2. CAMBIOS MINIMOS EN CODIGO

No se cambio la logica principal del sistema ni la estructura de tablas.

Solo se hicieron estos cambios minimos para que lo que ya existia pueda responder:

- Archivo: routes\api.php
  - Se agrego la ruta GET /api/aerolineas
  - Motivo: la vista principal ya consumia /api/aerolineas, pero esa ruta no estaba registrada

- Archivo: resources\views\simulacion.blade.php
  - Se cambiaron estas URLs:
    - de: http://127.0.0.1:8000/api/vuelos
    - a:  /api/vuelos
    - de: http://127.0.0.1:8000/api/aerolineas
    - a:  /api/aerolineas
  - Motivo: para que funcione igual en Laragon, XAMPP o artisan serve sin quedar amarrado a una URL fija

3. RUTAS IMPORTANTES DONDE SE TOCA LA CONEXION O EL ACCESO

Si despues se necesita conectar el proyecto en otra computadora o en otro entorno, estos son los archivos exactos a revisar:

- Conexion principal de base de datos:
  - C:\xampp\htdocs\logistica-aerea\.env
  - Variables usadas:
    - DB_CONNECTION=mysql
    - DB_HOST=127.0.0.1
    - DB_PORT=3306
    - DB_DATABASE=logistica
    - DB_USERNAME=root
    - DB_PASSWORD=

- Configuracion base de Laravel para BD:
  - C:\xampp\htdocs\logistica-aerea\config\database.php
  - Aqui Laravel lee los valores del .env
  - Normalmente no hace falta modificar este archivo si el .env esta bien

- Ruta API que la vista usa para cargar aerolineas:
  - C:\xampp\htdocs\logistica-aerea\routes\api.php
  - Ruta agregada:
    - GET /api/aerolineas

- Vista principal donde se llaman los endpoints:
  - C:\xampp\htdocs\logistica-aerea\resources\views\simulacion.blade.php
  - Endpoints usados:
    - /api/vuelos
    - /api/aerolineas

- Respaldo SQL original:
  - C:\xampp\htdocs\logica_aerea.sql

- Copia compatible para importar en MariaDB:
  - C:\xampp\htdocs\logistica-aerea\database\logica_aerea_mariadb.sql

4. COSAS QUE NO SE TOCARON

- No se cambio la estructura de la base de datos del proyecto
- No se cambiaron migraciones
- No se cambiaron modelos
- No se cambiaron controladores para alterar reglas del negocio
- No se rediseño la interfaz
- No se hizo una "correccion total" del sistema

5. IMPORTANTE PARA EJECUTAR

- El proyecto usa Laravel 10
- Laravel 10 necesita PHP 8.1 o superior
- En XAMPP habia PHP 8.0.30, por eso no corria artisan
- Se comprobo que si funciona con PHP de Laragon 8.3.26

Ejemplo para levantarlo:

C:\laragon\bin\php\php-8.3.26-Win32-vs16-x64\php.exe artisan serve

Luego abrir:

http://127.0.0.1:8000

6. RESUMEN CORTO

Lo que se cambio fue principalmente para compatibilidad del entorno y para que el sistema use la base que ya esperaba usar.
No se cambio el proyecto "porque si", ni se rehizo su funcionamiento.
