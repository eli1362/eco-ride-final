<?php
include '../../config/Database.php'; // Ensure this file has the database connection setup

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $depart = htmlspecialchars($_POST['depart']);
    $destination = htmlspecialchars($_POST['destination']);
    $date = htmlspecialchars($_POST['date']);
    $time = htmlspecialchars($_POST['time']);
    $passenger = intval($_POST['passenger']);
    $carType = htmlspecialchars($_POST['carType']);

    if (empty($depart) || empty($destination) || empty($date) || empty($time) || empty($carType)) {
        echo "All fields are required.";
        exit();
    }

    header("Location: ../driverResultPage.php?depart=$depart&destination=$destination&date=$date&time=$time&passenger=$passenger&carType=$carType");
    exit();
}

