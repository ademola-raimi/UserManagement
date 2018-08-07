# User Management System
[![Build Status](https://travis-ci.org/andela-araimi/UserManagement.svg?branch=master)](https://travis-ci.org/andela-araimi/UserManagement)

Welcome to User Management System. Zend Framework 3 is used to build this app.

## Introduction
<hr>
This is a platform where user can add their details. The homepage displays a simple form with 3 fields
`Last Name`, `First Name` and `Email`. When you fill the form. you get a success notification and you will be redirected to a list of other users including yours. 

Note that the form is highly validated. All fields are required. Email field is unique and that means that there will never be duplicated email. If you enter an already existed email or ignore any of the fields and click Save, you will be redirected back with error messages styled with special color.

Any user can be edited and deleted. If you click on the `EDIT` button on the list, you will be redirected to the `EDIT PAGE` where you can make your corrections, it's also validated.


If a user is deleted, the data will completely be removed from the database. It can't be recovered.

## Installation
<hr>
After clonning this project, the easiest way to start the server is by using the PHP's built-in web server:

```bash
$ cd path/to/install
# then you have to run the below command to get all the dependencies
$ composer update 
#when asked for an input enter “1”
$ php -S 0.0.0.0:8080 -t public/ public/index.php
# OR use the composer alias:
$ composer run --timeout 0 serve

# the project should be on localhost:8080
```

Everything is already setup in this project, you really don't need to do any other thing. sqlite is used as a data storage system. The project contains an sqlite database `sqlite.db` located in the data folder.

If you decide to use your own database. There is a schema.sql file in the data folder and there is also load_db.php file in the same folder. You can run edit the schema to change the db name and run the `load_db.php` to create the db, the table and some data:
```bash
$ php load_db.php
```

You can also decide to create the db and the table directly with sqlite in the CLI.

##Technology
`PHP/Zend Framework`, 'sqlite', 'Datatables', `Bootstrap`, Selenium, Codeception, Chrome driver

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
Or
`composer test`

#### NOTE! NOTE! NOTE!

I also took out time to write automation test. Codeception, Selenium server and chrome driver is used for this part. You don't need to install them, I already included the tools in the project

To run the test, run:
`vendor/bin/codecept run `
if you encounter an error of UserManagement/tests/functional/" and UserManagement/tests/unit/" directory does not exist directory does not exist, please make an empty directory to fix the error.
##### note that you need to start your server before running the test
##### you also need to start selenium server by running:
`java -jar selenium-server-standalone-3.13.0.jar`

This will open up chrome automatically and run all the UI tests

## Other methods of installation
<hr>

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

