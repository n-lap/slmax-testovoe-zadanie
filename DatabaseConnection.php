<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_DATABASE', 'People');

class DatabaseConnection
{
    private $connect;
    
    public function __construct()
    {
        $connect = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);

        if ($connect->connect_error) {
            die("<h1>Database Connection Failed</h1>" . mysqli_connect_error());
        }
        return $this->connect = $connect;
    }

    public function __get($connect)
    {
        return $this->$connect;
    }
}
