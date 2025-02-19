<?php

namespace auth;
class Database
{
    private static $con;

    public static function Connect()
    {
        self::$con = mysqli_connect("localhost", "root", "", "eco_ride") or die(mysqli_connect_error());
        return self::$con;

    }

}

Database::Connect();