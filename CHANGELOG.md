### Change Log

### 2.0.0
- Upgrades codebase for PHP 8.x compatibility (<8.x is no longer compatible).
- Upgrades Slim from 3.x to 4.x, as well as all Dependencies.
    - Request and Response objects now use Guzzle PHP library.
    - Dependency injection now uses Aura/DI library, instead of Slim/.
    - SQL queries now use FaaPZ/PDO library, instead of Slim/PDO.
- Upgrades forked Avro library for PHP 8.x compatibility.
- Upgrades Config library to use ENVIRONMENT environment variable.
    - isLocalEnvironment() now looks for ENVIRONMENT=local, rather than absence of LAMDA_TASK_ROOT variable.
    - loadConfiguration() loads Global environment file first, then loads config/[ENVIRONMENT].env, allowing overrides.
- Updates Swagger from 2.x to 3.x.
    - Docblocks now use @OA instead of @SWG tag for Open API.
    - Output will not be backward compatible with Swagger 2.x.

### 1.2.5
- Added option to return total count.

