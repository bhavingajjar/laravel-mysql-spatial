# AGENTS.md

## Cursor Cloud specific instructions

This repo is a **single PHP/Composer library** (`bhavingajjar/laravel-mysql-spatial`) that adds
MySQL spatial data types/functions to Laravel Eloquent. There is **no long-running app** to start —
"running it" means executing the PHPUnit suites. Standard commands live in `composer.json`
(`test`, `test:unit`, `test:integration`), `Makefile`, and `phpunit.xml.dist`.

### Toolchain
- Use **PHP 7.4** (matches `.travis.yml`; `phpunit ~6.5` and `laravel/laravel ^8.0` are not PHP 8 compatible). `php` already resolves to 7.4 via `update-alternatives`.
- Composer 2.x is installed globally. The repo has **no `composer.lock`** (git-ignored), so `composer install` resolves the latest packages allowed by `composer.json`.
- Composer 2.x blocks packages with security advisories by default; `phpunit 6.5` / `laravel 8` trip this. The advisory policy is disabled via global config (`composer config --global policy.advisories.block false`), which the update script re-applies. Do **not** edit `composer.json` to work around it.

### Database (only needed for the integration suite)
- The unit suite (`composer test:unit`) needs **no database**.
- The integration suite (`composer test:integration`) needs MySQL on `127.0.0.1:3306`, database `spatial_test`, user `root`, **empty password** (see `phpunit.xml.dist`).
- The repo's `Makefile`/`docker-compose.yml` expect Docker, which is **not installed** here. Instead, **MySQL 8.0 is installed natively**. The update script does not start it — start it yourself each session:
  - `sudo service mysql start`
  - `root@127.0.0.1` is configured with `mysql_native_password` and an empty password, and the `spatial_test` database exists (persisted in the VM snapshot). If missing after a fresh DB, recreate with:
    `sudo mysql -e "ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY ''; CREATE USER IF NOT EXISTS 'root'@'127.0.0.1' IDENTIFIED WITH mysql_native_password BY ''; GRANT ALL PRIVILEGES ON *.* TO 'root'@'127.0.0.1' WITH GRANT OPTION; CREATE DATABASE IF NOT EXISTS spatial_test;"`

### Lint / coverage
- No linter is configured (no PHPCS/PHPStan/CS-Fixer). There is no build step beyond Composer autoload generation.
- Code coverage (`--coverage-clover`) needs Xdebug/pcov, which is not installed; PHPUnit prints "No code coverage driver is available" but tests still run.

### Known pre-existing test failures (not environment issues)
Because there is no lockfile, `composer install` pulls the newest Laravel 8.x and MySQL 8.0.46, which differ from when the tests were written:
- A few tests assert `add spatial \`..._spatial\`` / `SPATIAL KEY \`..._spatial\``, but Laravel 8.83 generates `..._spatialindex`.
- `SridSpatialTest::testInsertPointWithWrongSrid` expects an error string without `'axis-order=long-lat'`, which MySQL 8.0.46 now appends.
Do not "fix" these by changing library code unless that is the actual task.
