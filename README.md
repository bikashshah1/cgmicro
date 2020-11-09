# CGTest Only


This project uses docker-compose to setup Lumen-based micro-services that does a based CRUD operation on Items. It uses PHPUnit for Unit Testing. The tech stack used are nginx, phpfpm, mysql8 - as described in docker-compose.yml file. 

Lumen is a very robust micro-framework fork of Laravel. This stack is a good fit for micro-services development as well deployment. nginx can be configuired for load-balancing, though an API Gateway would be a better choice. This project can be easily deployed into kubernetes as well. 



## Installation

This assumes that that docker and docker-compose is already installed in the machine (Linux, Windows and Mac version of docker can be downloaded from docker.io)



First, clone the application, cd into the cgmicro folder and run the docker-compose up -d command as such. 
```Bash
git clone git@github.com:bikashshah1/cgmicro.git
cd cgmicro
docker-compose up -d
```

This will create 3 different containers and setup the application for you. 



Please Note: docker-compose.yml is configured to expose port 80 of docker service to 8080 of local machine. If the local machine is already using port 8080, please edit docker-compose.yml file port mapping accordingly. 


There is a final step - the initial migration needs to be run. For that first log into the phpfpm instance as such:

```Bash
docker-compose exec phpfpm /bin/bash
```

Next, run php artisan migrate command
```Bash
cd /var/www/html
php artisan migrate
```

The app is now ready to be used. The Lumen project is setup and can be accessed from http://localhost:8080/items. 

To run the UNIT Test (I have used PHPUnit provided by default in Lumen), once logged in to phpfpm instance


```Bash
cd /var/www/html
./vendor/bin/phpunit
```


## Not In Scope.

- The project doesn't have any authentication setup as this micro-service can be setup behind an API gateway that then takes care of throttling, authentication, rate-limiting, load-balancing and many more. 

- The project isn't using Redis or another Cache driver - that is a DevOps configuration and quick facade can be added to achieve improved throughput 




References:

https://github.com/moby/moby/issues/23371