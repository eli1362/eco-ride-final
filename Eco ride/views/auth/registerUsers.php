<?php
include_once 'Database.php';

class registerUsers
{
    private static $con;
    public static function selectUser(): mysqli_result|bool
    {
        self::DataBaseConnect();
        $query = mysqli_query(self::$con , "select * from users");
        return $query;
    }
    public static function InsertUser($full_name,$email,$password): void
    {
        self::DataBaseConnect();
        mysqli_query(self::$con,"insert into users(full_name,email,password) values ('$full_name','$email','$password')");

    }
    public static function DataBaseConnect(): void
    {
        self::$con = \auth\Database::Connect();
        mysqli_set_charset(self::$con,"utf8");

    }

}