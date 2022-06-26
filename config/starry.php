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
    "starry_repository_path" => env("STARRY_REPOSITORY_PATH", "Eloquent")

];