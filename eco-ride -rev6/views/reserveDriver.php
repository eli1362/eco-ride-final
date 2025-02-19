<?php
// Enable error reporting for debugging
global $db;
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
include '../config/Database.php'; // Include your database connection file

// Check the connection
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $driver_id = intval($_POST['driver_id']);
    $user_id = 1;  // Replace with actual user ID
    $depart = htmlspecialchars(trim($_POST['depart']));
    $destination = htmlspecialchars(trim($_POST['destination']));
    $date = htmlspecialchars(trim($_POST['date']));
    $time = htmlspecialchars(trim($_POST['time']));
    $passenger = intval($_POST['passenger']);
    $car_type = htmlspecialchars(trim($_POST['car_type']));

    // Insert the reservation into the database
    $stmt = $db->prepare("
        INSERT INTO reservations (user_id, driver_id, depart, destination, date, time, passenger, car_type)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("iisssiss", $user_id, $driver_id, $depart, $destination, $date, $time, $passenger, $car_type);

    if ($stmt->execute()) {
        echo "Reservation successful!";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
    $db->close();
} else {
    echo "Invalid request method.";
}