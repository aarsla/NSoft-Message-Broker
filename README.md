# NSoft Task - Aid Arslanagic

Task is developed in PHP 5.6.29 with Symfony 2.8 framework using RabbitMQ 3.6.11 and MySQL 5.7.16 database.

* [PHP](http://php.net) - Hypertext processor
* [Symfony](https://symfony.com) - High perofrmance PHP framework
* [RabbitMQ](https://www.rabbitmq.com) - Open source message broker
* [MySQL](https://www.mysql.com) - Open source database

# Installation (OSX)

> Note: Should be really distributed as a docker image

Make sure [Homebrew](https://brew.sh) is installed before continuing. Then install required software

```sh
$ brew install php-56
$ brew install mysql
$ brew install rabbitmq
```
Update project dependencies
```sh
$ cd ~/project
$ composer install
```

# Configuration 

### RabbitMQ
 - User 'guest' with 'guest' credentials
### Mysql 
 - User 'root' without a password and 'storage' database

# Usage

Initialize database:
```sh
$ app/console d:s:c
```
Open several terminals and in each one start a consumer (Service B).
```sh
$ app/console consumer:start
```
Consumers will bind to 'post_office' queue and require message acknowlegdment with prefetch count of 1.

Start internal web server (simple web frontend for Service A)
```sh
$ app/console s:r
```
Open http://localhost:8000 to access web frontend. You can now send test messages to Service B through RabbitMQ 'post_office' durable exchange.

### Stress test
Use [RabbitMQ Management plugin](https://www.rabbitmq.com/management.html) to monitor message processing. 

Send bulk messages to Service B
```sh
$ app/console producer:spam -m 10000
```
You can send up to 10000 messages to Service B workers.

### Todos

- Optimize message handling and communication with storage
- Scale RabbitMQ and database
- Improve web frontend for real time responsivness
- Add mobile clients
- ...