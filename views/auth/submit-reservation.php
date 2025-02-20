<?php

// Enable error reporting for debugging
global $db;
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
include '../../config/Database.php';

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize form data
    $depart = htmlspecialchars(trim($_POST['depart']));
    $destination = htmlspecialchars(trim($_POST['destination']));
    $date = htmlspecialchars(trim($_POST['date'])); // Format: dd/mm/yyyy
    $passenger = intval($_POST['passenger']);
    $carType = htmlspecialchars(trim($_POST['carType']));
    $time = htmlspecialchars(trim($_POST['time']));

    // Validate inputs
    if (empty($depart) || empty($destination) || empty($date) || empty($time) || empty($carType)) {
        echo "All fields are required.";
        exit();
    }

    // Convert date format from dd/mm/yyyy to yyyy-mm-dd for SQL
    $dateParts = explode('/', $date);
    if (count($dateParts) !== 3) {
        echo "Invalid date format.";
        exit();
    }
    $formattedDate = $dateParts[2] . '-' . $dateParts[1] . '-' . $dateParts[0];

    // Prepare and execute the SQL statement
    $stmt = $db->prepare("INSERT INTO reservations (depart, destination, date, passenger, car_type, time) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssiss", $depart, $destination, $formattedDate, $passenger, $carType, $time);

    if ($stmt->execute()) {
        echo "Reservation successful!";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement and connection
    $stmt->close();
    $db->close();
} else {
    echo "Invalid request method.";
}
