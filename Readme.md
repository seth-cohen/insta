## Part 1: Shopper Applicants ##

### Usage ###
- Requires PHP 7.1 
  - [Install / upgrade instructions][install-php]
- I have included the composer.phar archive in the repo if you already have composer installed globally or 
with the phar location in your path then you don't really need this if not read on
  - from the root application directory you will simply run the command: 
  ```
   php composer.phar install
  ```
  - This will ensure that you have all of the correct dependencies installed on the system
  - save the applicants.sqlite3 file in `<application_root>/db` directory that you will need to create
  - Once installed you should just have to:
  ```
    php composer.phar start
  ```
  - The above command will use PHP's built in webserver (nothing fancy by any means) and the application will be available
  at [here][landing-page] (localhost:8080/shopper/landing)
- The application will persist new shoppers into the sqlite3 provided database. Additionally, there is a log file at 
 `application_root/logs/<date>app.log` that details the status of the various operations
  - The database operations were all wrapped in try catch with the exception being swallowed so that flow will continue
  in the case that you did not move the sqlite file into the DB directory
  

### Tech stack ###
- sqlite3 database
- Serverside language - PHP
- Framework - Slim
    - Note that slim is a micro framework that supports most of the PSR-7 response and request interfaces as
    well as providing routing functionality. Additionally, it has support to inject different renderers.
    - One main reason for choosing Slim is how light weight it is.
    - An additional reason is for the integration with DIC (dependency injection container) - This application makes heavy
    use of the container to create modular and testable components.
    - Slim also does not enforce any paradigms on you. As such it is up to you to develop the MVC/Layered architecture and 
    layer it on top of the routing and request/response framework that it provides
    - Had there been additional time there would be:
       - unit tests
       - additional interfaces to implement
       - finished docblocks - some aren't completely filled out
- Rendering - Twig template language (see next bullet)
- Front End - Materialize CSS framework and just some light JS
    - Had there been additional time would have considered ReactJS with PHPs V8JS for server side rendering functionality
    - Componentized front ends have many benefits however, I didn't feel as though I would have had enough time to complete 
    the challenge and set up all of the infrastructure necessary to support (babel transforms, webpack, etc)

## Part 2: Applicant Analysis ##

### Usage ###
- Ensure that the applicants.sqlite3 db file is located in `<application_root>/db` directory
- From the `<application_root>/analytics` directory simply run following command:
    ```
        ./analysics-cpp "yyyy-mm-dd" "yyyy-mm-dd"
    ```
    - where first argument is the start date and the second argument is the end date of the range
     for which you want to group applicants

### Tech stack ###
- This was built using C/C++ and the sqlite3 library
- Decided to go with C++ since the problem description indicated that speed was of the utmost importance

### Please reach out if you have any questions ##

[install-php]: https://developerjack.com/blog/2016/installing-php71-with-homebrew/
[landing-page]: localhost:8080/shopper/landing
