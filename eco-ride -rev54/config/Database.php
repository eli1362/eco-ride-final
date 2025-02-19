<?php

// Database connection settings
$host = 'localhost';  // The database server (usually localhost)
$dbname = 'eco_ride';  // The name of your database
$username = 'root';   // The database username
$password = '';

// Create a new mysqli connection
try {
    $db = new mysqli($host, $username, $password, $dbname); // $db is the connection object
    if ($db->connect_error) {  // Check for connection errors
        die("Connection failed: " . $db->connect_error); // Terminate if connection fails
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();  // Handle any other exceptions
    die();
}