# Starry

Starry is a small package for creating a standard repository system in Laravel(PHP Framework).

## Installation

TODO

```bash
TODO
```

## Usage

### Launch Starry

```bash
php artisan starry:launch
```

This command will launch a basic setup for the starry and publish a config file ```starry.php``` to manage configration of starry package.

#### Change Configration

If you want to use other folder structure then you can change this by changing the ```.env``` variables


###### If we followed the default setup then this config file will appear
```
<?php

return [

    /*
    * ***************************************
    * Which data model our project is using *
    * ***************************************
    */
    "starry_data_model" => env('STARRY_DATA_MODEL', "Eloquent"),

    /*
    * ********************************************
    * Path where we want to store out interfaces *
    * ********************************************
    */
    'starry_interfaces_path' => env("STARRY_INTERFACES_PATH", "StarryInterfaces"),

    /*
    * ********************************************
    * Where We want to store our main repository *
    * ********************************************
    */
    "starry_repository_path" => env("STARRY_REPOSITORY_PATH", "Eloquent"),

    'bindings' => [
		\App\Repository\StarryInterfaces\EloquentRepositoryInterface::class => \App\Repository\Eloquent\BaseRepository::class,

	],
];

```

Initially Starry will follow this structure according to the above configration.
```
---App
------Repository
----------------Eloquent(Default Setup)
---------------------------------------BaseRepository.php
----------------StarryInterfaces(Default Setup)
----------------------------------------------EloquentRepositoryInterface.php(Default Setup)
```

All repositories other than the default one will extend the ```BaseRepository``` just to reduce the code. 


### Create your Starry

```bash
php artisan make:starry model_name
```
This command required a ```model_name``` for which you want to create repository and interface

#### For Instance

```bash
php artisan make:starry User
```
This will create ```UserRepositoryInterface```, ```UserRepository``` and bind them to our laravel app.

### Binding

##### How binding actually work for starry?

In ```starry.php``` inside our config folder we have our all bindings.

```
    'bindings' => [
      \App\Repository\StarryInterfaces\EloquentRepositoryInterface::class => \App\Repository\Eloquent\BaseRepository::class,

    ],
```

So if you have created your Repository manually and you want to bind this with the help of ```starry```
simply add your ```interface``` as the ```key``` and ```repository_class``` as ```value``` 
for the element in ```bindings``` array

## License
[MIT](https://choosealicense.com/licenses/mit/)