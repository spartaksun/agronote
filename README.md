# AgroNote

### To start project locally:

Clone reppository
```shell
git clone https://github.com/spartaksun/agronote.git
```
Install dependencies
```shell
cd arronote
composer install
```
Warmup a cache
```shell
bin/console cache:warmup
```
Start a Postgres and forward 5432 to 5432
```shell
docker-compose up 
```

Init database
```shell
bin/console doctrine:schema:update --force --complete
```

Create admin
```text
bin/console create:admin 
```

Run a web server
```shell
php -S 127.0.0.1:8000 public/index.php
```

Open http://127.0.0.1:8000 and Register new user.

Create some test tasks
```shell
bin/console generate:test-data
```
Login as admin using credentials admin:admin