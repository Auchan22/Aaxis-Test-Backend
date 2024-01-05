# Aaxis Test Backend

Sistema desarrollado como parte de la evaluación ténica para Aaxis.

## 🔩 Paquetes utilizados
* **LexikJWT**: paquete utilizado para la autenticación por JWT (no se implemento)

## Pasos para la instalación
1) Copia del repositorio
   ```
    git clone <url-proyecto>
   cd aaxis-test-backend
   ```
   
3) Instalación de paquetes
   ```
    composer install
   ```

4) Generación de tablas en Base de datos
   ```
    php bin/console make:migration

   php bin/console doctrine:migrations:migrate
   ```

5) Inicio del servidor
   ```
    symfony server:start
   ```

## Detalles
* La autenticación no anda
* El endpoint de get productos no da las propiedades de los productos
