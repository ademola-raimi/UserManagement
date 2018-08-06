# User Management System
[![Build Status](https://travis-ci.org/andela-araimi/UserManagement.svg?branch=master)](https://travis-ci.org/andela-araimi/UserManagement)

Welcome to User Management System. Zend Framework 3 is used to build this app.

## Introduction
This is a platform where user can add their details. The homepage displays a simple form with 3 fields
`Last Name`, `First Name` and `Email`. When you fill the form. you get a success notification and you will be redirected to a list of other users including yours. 

Note that the form is highly validated. All fields are required. Email field is unique and that means that there will never be duplicated email. If you enter an already existed email or ignore any of the fields and click Save, you will be redirected back with error messages styled with special color.

Any user can be edited and deleted. If you click in the edited field on the table, you will also get a small form to make your edition and as expected, it's also validated the same way addition is validated.

If a user click on the delete link, the user will be prompted if he/she really wants to delete the user. If the user clicks `No`, the data will be retained and if `yes`, the data will completely be removed from the database. It can't be recovered.

## Installation
After clonning this project, the easiest way to start the server is by using the PHP's built-in web server:

```bash
$ cd path/to/install
# then you have to run the below command to get all the dependencies
$ composer update 
$ php -S 0.0.0.0:8080 -t public/ public/index.php
# OR use the composer alias:
$ composer run --timeout 0 serve
```

Everything is already setup in this project, you really don't need to do any other thing. sqlite is used as a data storage system. The project contains an sqlite database `sqlite.db` located in the data folder.

If you decide to use your own database. There is a schema.sql file in the data folder and there is also load_db.php file in the same folder. You can run edit the schema to change the db name and run the `load_db.php` to create the db, the table and some data:
```bash
$ php load_db.php
```

You can also decide to create the db and the table directly with sqlite in the CLI.

##Technology
`PHP/Zend Framework`, 'sqlite', 'Datatables', `Bootstrap`

## Tests
<hr>

if you have phpunit installed globally (recommended), run
`phpunit `
Otherwise, run
`./vendor/bin/phpunit`

If you are running PHP 7.2, to succesfully run the test, run 

`phpunit  --stderr `
Or
`./vendor/bin/phpunit --stderr`

## Other method of installation

### Using docker-compose

This skeleton provides a `docker-compose.yml` for use with
[docker-compose](https://docs.docker.com/compose/); it
uses the `Dockerfile` provided as its base. Build and start the image using:

```bash
$ docker-compose up -d --build
```

At this point, you can visit http://localhost:8080 to see the site running.

You can also run composer from the image. The container environment is named
"zf", so you will pass that value to `docker-compose run`:

```bash
$ docker-compose run zf composer install
```

### Using Vagrant

This skeleton includes a `Vagrantfile` based on ubuntu 16.04 (bento box)
with configured Apache2 and PHP 7.0. Start it up using:

```bash
$ vagrant up
```

Once built, you can also run composer within the box. For example, the following
will install dependencies:

```bash
$ vagrant ssh -c 'composer install'
```

While this will update them:

```bash
$ vagrant ssh -c 'composer update'
```

While running, Vagrant maps your host port 8080 to port 80 on the virtual
machine; you can visit the site at http://localhost:8080/

