
# joinable Backend (Team devhub)
 Joinable backend is backend of joinable project, joinable project is platform that make user join and feel belong to their office quicker, there are ton of infomation and guideline about their office that their admin post into application, users can see all of employee and who have same activitie and careers with them. Users can create event asscociate with any activities and let the other can join.


## Installation

### Install package

``` bash
composer i
```

### Copy .env.example to .env

``` bash
cp .env.example .env
```

### Change database in  .env

``` bash
DB_CONNECTION=mysql

DB_HOST=127.0.0.1

DB_PORT=3306

DB_DATABASE={{ your data base name }}

DB_USERNAME={{ your data base user name }}

DB_PASSWORD={{ your data base password }}
```

### Run migration

``` bash
php aritsan migrate --seed
```

### Generate App key

``` bash
php aritsan key:generate
```

### Install passport to generate key for Authentication

``` bash
php passport:install
```

### Run server

``` bash
php artisan serve
```
normaly the server will start on localhost:8000 if no one change APP_URL in .env
``` bash
APP_URL=http://localhost:8000
```