# Instrucciones de Despliegue - Tree API

## Requisitos Previos
*   PHP 8.2 o superior
*   Composer
*   Un servidor de base de datos (MySQL) o SQLite habilitado.

## Instalación

1.  **Clonar el repositorio**:
    ```bash
    git clone https://github.com/johan50693/Laravel_Tree_Api
    cd tree-api
    ```

2.  **Instalar dependencias de PHP**:
    ```bash
    composer install
    ```

3.  **Configurar entorno**:
    ```bash
    cp .env.example .env
    ```
    *   Editar el archivo `.env` configurando la base de datos. Para pruebas rápidas con SQLite:
        *   Crear archivo `database/database.sqlite`.
        *   En `.env`: `DB_CONNECTION=sqlite` y borrar las otras config de DB.

4.  **Generar clave de aplicación**:
    ```bash
    php artisan key:generate
    ```

5.  **Ejecutar migraciones y seeders**:
    ```bash
    php artisan migrate --seed
    # O específicamente:
    # php artisan migrate
    # php artisan db:seed --class=NodeSeeder
    ```

## Ejecución del Servicio

Para desarrollo local:
```bash
php artisan serve
```
El API estará disponible en `http://localhost:8000/api`.

## Documentación API (Swagger)

Una vez en ejecución, la documentación se encuentra en:
`http://localhost:8000/api/documentation`  
*(Nota: Requiere generar los docs con `php artisan l5-swagger:generate` si se ha instalado Swagger)*

## Endpoints Principales

*   `POST /api/nodes`: Crear nodo. `{ parent: id_opcional }`.
*   `GET /api/nodes/parents`: Listar raíces.
*   `GET /api/nodes/{id}/children?depth=1`: Listar hijos.
*   `DELETE /api/nodes/{id}`: Eliminar nodo.

## Headers Soportados

*   `Accept-Language`: `es`, `en`, `fr`... (Para traducción de títulos numéricos).
    *   Ejemplo: ID 1 en `en` -> "one", en `es` -> "uno".

## Parametros Soportados

*   `timezone`: Zona horaria para `created_at`. Ejemplo: `America/Caracas`.
