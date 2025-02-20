<?php
global $db;
session_start();
include('../config/Database.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve reservation data from the form
    $driver_id = $_POST['driver_id'];
    $depart = $_POST['depart'];
    $destination = $_POST['destination'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $passenger = $_POST['passenger'];
    $car_type = $_POST['car_type'];

    // Assuming user is logged in, get user ID from session
    $user_id = $_SESSION['user_id'];

    // Insert reservation data into the database
    $sql = "INSERT INTO reservations (depart, destination, date, time, passenger, car_type, driver_id, user_id)
            VALUES ('$depart', '$destination', '$date', '$time', '$passenger', '$car_type', '$driver_id', '$user_id')";

    if ($db->query($sql) === TRUE) {
        echo "Reservation successfully made!";
    } else {
        echo "Error: " . $sql . "<br>" . $db->error;
    }

    // Redirect or display a message
    header('Location: confirmation_page.php'); // Redirect to a confirmation page or display a success message
}
