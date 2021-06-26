# ORDERS-EXERCISE

Hey, this is my approach to the exercise. 
I've used Symfony 5.3 to create the Rest API. 

## Setting up the project

To setup the project it's a must to have MySQL and Symfony CLI installed, at least this is the fatest way to start the project.
You can check through here https://symfony.com/download how to install the cli.

Once you have MySQL and Symfony CLI installed, you can comeback here and clone the project.

After cloning, go into the .env file and change line 26 to setup with your mysql user, you can use root.

So if your connection is good, you'll be able to run the following commands:

``php bin/console d:d:c`` (Creates the database)

``php bin/console d:s:u --force`` (Updates the schema)

``php bin/console d:f:l`` (This loads the fixtures that I've created so you don't need to work manually inserting all the products)

After that, you can start your server just by running

``composer install``

``symfony server:start``

Now everything is up for you to start doing requests to the API at http://127.0.0.1:8000 (or the port defined by symfony)

## Endpoints

I've created several endpoints for the api, you can find a file in the root of the project called requestCollection
which contains all of the endpoints.

Here are some if you want to do them manually:

- /order (GET, POST) *
- /order/{id} (GET) *
- /order/{id}/pay (PUT) * 

- /payment-method (GET, POST)
- /payment-method/{id} (GET)

- /product (GET, POST)
- /product/{id} (GET)


/** The ones with '*' are the ones that where required by the exercise.
