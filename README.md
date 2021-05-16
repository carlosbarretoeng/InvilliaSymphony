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

![img1.png](https://raw.githubusercontent.com/carlosbarretoeng/InvilliaSymphony/master/img1.png)

You can click on "Choose a file ..." to select one or **multiple** files. 
The app will validate the files over mime type and size, based on the php.
ini post_max_upload property. You can drag and drop files over there too. 

After that, you will click on the "Process" button to upload and processing 
the files. While the files are uploading, a blue label as follows still on 
the screen.

![img2.png](https://raw.githubusercontent.com/carlosbarretoeng/InvilliaSymphony/master/img2.png)

If something wrong occurs, a red label as follows will be shown. The text
on the label will explain the error.

![img3.png](https://raw.githubusercontent.com/carlosbarretoeng/InvilliaSymphony/master/img3.png)

When all files and elements are processed, a green label message will be 
on the screen. So now, the API can bring back this new data.

![img4.png](https://raw.githubusercontent.com/carlosbarretoeng/InvilliaSymphony/master/img4.png)

The button "API Documentation" will send you to a page with API specs.

I hope that this app can show you a few of my tech skills. I know that I 
have so much to improve, but every day I still studying. Thanks a lot 
for this opportunity!

### Have an incredible day and keep safe!