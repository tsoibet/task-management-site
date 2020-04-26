# Task management site

## Environment preparation
In this project, Docker will be used to set up the local env. 
### Advantage of using Docker
1. Standardization. 
Docker provides repeatable development, build, test, and production environments.
2. Compatibility and Maintainability. 
Images run the same no matter which server or whose laptop they are running on. 
3. Simplicity and Faster Configurations. 
Users can take their own configuration, put it into code, and deploy it without any problems.
4. Rapid Deployment. 
Docker manages to reduce deployment to seconds. This is due to the fact that it creates a container for every process and does not boot an OS.
5. Isolation.
Docker ensures the application and resources are isolated and segregated. Docker makes sure each container has its own resources that are isolated from other containers.

### Setup
1. Download and install docker if not available: 
https://docs.docker.com/docker-for-mac/install/

2. Create a working directory. E.g.:
```bash
$ mkdir ~/Developement/task_management_site
$ cd ~/Developement/task_management_site
```
3. Open the directory with an IDE/ Editor (vs code used here)
```bash
$ code ~/Developement/task_management_site
```
4. Create a `docker-compose.yml` file in the working directory
5. Edit the `docker-compose.yml` with following content: 
```yml
version: '3'
services:
    nginx:
        image: nginx:latest
        ports:
            - "8080:80"
        depends_on:
            - "php"
        volumes:
            - "$PWD/conf.d:/etc/nginx/conf.d"
            - "$PWD/html:/usr/share/nginx/html"
            - "$PWD/html:/var/www/html"
        container_name: "compose-nginx"
    php:
        build: ./php-mysqli
        image: php:7.2-fpm-mysqli
        environment:
            - MYSQL_HOST=mysql
            - MYSQL_USER=root
            - MYSQL_PASS=secret
            - MYSQL_DB=mysql
            - MYSQL_PORT=3306
        ports:
            - "9000:9000"
        volumes:
            - "$PWD/html:/var/www/html"
        container_name: "compose-php"
    mysql:
        image: mysql:5.7
        ports:
            - "3306:3306"
        environment:
            - MYSQL_ROOT_PASSWORD=secret
        container_name: "compose-mysql"
    adminer:
        image: adminer
        restart: always
        ports:
            - "8088:8080"
```
6. Create a folder named `php-mysqli` in working directory with a file inside named `dockerfile` with following content:
```dockerfile
FROM php:7.2-fpm
RUN apt-get update \
  && apt-get -y install iputils-ping \
  && docker-php-ext-install mysqli \
  && docker-php-ext-install pdo_mysql \
  && docker-php-ext-enable mysqli
```
7. Create a folder named `conf.d` in working directory with a file inside named `nginx.conf` with following content:
```conf
server {
    listen       80;
    server_name  localhost;
    location / {
        root   /var/www/html;
        index  index.html index.htm index.php;
    }
    error_page   500 502 503 504  /50x.html;
    location = /50x.html {
        root   /var/www/html;
    }
    location ~ \.php$ {
        fastcgi_pass   php:9000;
        fastcgi_index  index.php;
        fastcgi_param  SCRIPT_FILENAME  /var/www/html/$fastcgi_script_name;
        include        fastcgi_params;
    }
}
```
8. Create a folder named `html` in working directory with a file named `info.php` with following content:
```php
<?php
phpinfo();
```
9. Open terminal, ensure that your current directory is `~/Developement/task_management_site` (use `pwd` command) and start docker:
```bash
$ docker-compose up
```
9. Open browser and go to `localhose:8080/info.php`. If you see a page with a header bar with `PHP Version 7.x.x` and a php logo, congratulations, set up is done.

## Architecture
In the [Environment preparation](#environment-preparation) part, we set MySQL, PHP and nginx up and will use them as our main architecture.
### MySQL
>MySQL is a multithreaded, multi-user, SQL database management system (DBMS).
### PHP
>PHP is a server-side scripting language designed for web development but also used as a general-purpose programming language. PHP code is interpreted by a web server via a PHP processor module, which generates the resulting web page. PHP commands can optionally be embedded directly into an HTML source document rather than calling an external file to process data. It has also evolved to include a command-line interface capability and can be used in standalone graphical applications.[wiki](https://en.wikipedia.org/wiki/LAMP_(software_bundle)#PHP_and_alternatives)
### nginx
>nginx is a web server which can also be used as a reverse proxy, load balancer, mail proxy and HTTP cache.
### Adminer
>Besides the components above, adminer is also included in the docker-compose. Adminer is a light-weight tool for managing content in MySQL, PostgreSQL, MS SQL, SQLite and Oracle databases.
## Database(DB)
To keep the design of the application simple, we only need one table in the DB. 
```sql
DROP TABLE IF EXISTS `list`;
CREATE TABLE `list` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  `item` TEXT NOT NULL,
  `status` ENUM('to-do', 'in progress', 'done') NOT NULL,
  `created_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```
* `AUTO_INCREMENT` in the `id` column will insert and increase the id automatically every time when new row inserted.
* `ENUM` is a data type in MySQL which allow user to input specific value only. [ref.](https://dev.mysql.com/doc/refman/8.0/en/enum.html)
* `CURRENT_TIMESTAMP` in the `created_at` column will insert the current time automatically every time when new row inserted.

## Backend
The minimum funtionality should be `create`, `read`, `change status` and `delete` todo items.
### Create
1. Get input from user input.
2. Insert data to DB.
3. Notify user the data is stored.

### Read
1. Retrive all todo items from DB.
2. Group them into different status.
3. Show users all the items.

### Change status
1. Get the `id` and the `new status` of an item
2. Update the todo item in DB.
3. Notify user the data is updated.

### Delete
1. Get the `id` of an item
2. Delete the todo item in DB.
3. Notify user the data is deleted.

## Frontend
### Page(s)
The application can be either only one page or multiple pages.
Here are some advices for each design:
- If there is only one page, the functions is better to be organized separately. Or the functions should be stored in different php files.
- If there are multiple pages, a common part of a webpage is better to be separated into a different php file and that file should be repeatly used in all these pages. For example, the header part for the site should be the same in all pages, then there should be a `header.php`.
Regarding how to use content in different php files within a php file, you may use the `include_once` function. [ref.](https://www.php.net/manual/en/function.include-once.php)

### Frontend Design
To reduece the time of making design by yourself, Using the existing resources is highly recommended. 

- https://getbootstrap.com/
- [and more...](https://scotch.io/bar-talk/6-popular-css-frameworks-to-use-in-2019)

