
## ARM Backend developemnt Assesment


## Installation

- Ensure you have php installed on your machine or you are using Xampp Appache server which comes with PHP by default
- Ensure you have composer installed on your machine aswell (composer in a php dependency manager just like npm in Node )

Clone the repository `git clone <repo-url>`

After cloning, 
    
    cd <into the project folder>
    
After that 
    run this command to install all dependencies `composer install` 
    this will also install laravel passport which we will use for our api authentication
    
## Setup 
We need to setup up a database but for us to do that we have to create a `.env` file.
Git ignores this file when files are pushed to github, so to create it 

run this comman in your terminal `cp .env.example .env`

This command will create a .env file and copy the content of .env.example to it.
Don't worry editing the newly created .env file as all the right configuration are already preconfigured for you

**Alternatively**

You can manually create a `.env` file in the root directory and copy the content of `.env.example` into it.

## Create database
In this project I  used mysql therefore we are going to setup a mysql database 
Open `.env file` that you just created and locate the `DB_CONNECTION` section of the code

Ensure the database name is the same with one you created in your phpmyadmin otherwise change it to correspond with what you have in the .env file
    
#### Now lets create the database tables
open your terminal make make sure you are inside the application root directory/folder

once that is done 

run `php artisan migrate` to migrate your database tables.

After the migration, the application is good to go, all you have to do is run `php artisan serve` to start up you local server and test out the API using postman or any API testing tool.

#### Let us configure my laravel passport for authentication

To configure our laravel passport we just need to run two commands

`php artisan passport:install` this command will install the client secrects that passport uses for authentication

Next command is 

`php artisan passport:kyes` this command will create an oauth public and private keys


## Valid API endpoints

All the api endpoint and documentation is provided in a postman documentation here 
documentation link 
