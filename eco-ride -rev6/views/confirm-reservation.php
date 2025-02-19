<?php
global $db;
include '../config/Database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $driver_id = intval($_POST['driver_id']);
    $user_input = json_decode($_POST['user_input'], true);

    $stmt = $db->prepare("INSERT INTO reservations (depart, destination, date, passenger, car_type, time, driver_id) 
                           VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param(
        "sssissi",
        $user_input['depart'],
        $user_input['destination'],
        $user_input['date'],
        $user_input['passenger'],
        $user_input['carType'],
        $user_input['time'],
        $driver_id
    );

    if ($stmt->execute()) {
        echo "Reservation successful!";
    } else {
        echo "Error: " . $stmt->error;
    }
}

