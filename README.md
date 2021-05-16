# Invillia - PHP Challange

Hi there!

This challenge is to create an application that can upload XML files and expose
his data over an API for other apps. To make this, I must have implemented the 
software using the Symfony PHP framework.


## How to run

First, your need to clone the repository and build up the docker  containers
> $ git clone https://github.com/carlosbarretoeng/InvilliaSymphony.git 

> $ docker-compose up --build

After this, we need to install all vendor's libraries. To do that, in another 
terminal, inside your cloned folder, run:
> $ docker-compose run --rm composer install

Now, we need to bring the database alive. We will create and populate than 
with the following commands:
> $ docker-compose run --rm php bin/console doctrine:database:create

> $ docker-compose run --rm php bin/console doctrine:migrations:migrate --no-interaction

Done! Now you can access in your browser the URL [http://localhost:8080/](http://localhost:8080/)
and you can see this screen:

![img1.png](img.png)