<?php
    include "../config.php";

    function dbConnect() {
        $server = SERVER;
        $username = USERNAME;
        $password = PASSWORD;
        $database = DATABASE;
    
        if (empty($server) || empty($username) || empty($password) || empty($database)) {
            throw new Exception("Database connection parameters cannot be empty.");
        }
    
        //connection to database
        $conn = pg_connect("host=$server dbname=$database user=$username password=$password");
    
        if (!$conn) {
            throw new Exception("Database connection failed: " . pg_last_error());
        }
    
        return $conn;
    }