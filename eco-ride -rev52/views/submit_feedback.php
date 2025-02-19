<?php
global $db;
session_start();
include_once "../config/Database.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $carpool_id = $_POST['carpool_id'];
    $rating = $_POST['rating'];
    $feedback = $_POST['feedback'];

    // Get Driver ID
    $sql = "SELECT driver_id FROM carpool WHERE carpool_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $carpool_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $carpool = $result->fetch_assoc();
    $stmt->close();

    if (!$carpool) {
        $_SESSION['error_message'] = "Invalid trip.";
        header("Location: passenger_dashboard.php");
        exit();
    }

    $driver_id = $carpool['driver_id'];

    // Store feedback
    $sql = "UPDATE carpool SET feedback = ? WHERE carpool_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("si", $feedback, $carpool_id);
    $stmt->execute();
    $stmt->close();

    // Insert rating
    $rating_sql = "INSERT INTO driver_ratings (driver_id, rating) VALUES (?, ?)";
    $rating_stmt = $db->prepare($rating_sql);
    $rating_stmt->bind_param("ii", $driver_id, $rating);
    $rating_stmt->execute();
    $rating_stmt->close();

    // Calculate new average rating
    $avg_sql = "SELECT AVG(rating) AS avg_rating FROM driver_ratings WHERE driver_id = ?";
    $avg_stmt = $db->prepare($avg_sql);
    $avg_stmt->bind_param("i", $driver_id);
    $avg_stmt->execute();
    $result = $avg_stmt->get_result();
    $row = $result->fetch_assoc();
    $average_rating = round($row['avg_rating'], 1);
    $avg_stmt->close();

    // Update driver table
    $update_driver_sql = "UPDATE drivers SET average_rating = ? WHERE driver_id = ?";
    $update_driver_stmt = $db->prepare($update_driver_sql);
    $update_driver_stmt->bind_param("di", $average_rating, $driver_id);
    $update_driver_stmt->execute();
    $update_driver_stmt->close();

    $_SESSION['success_message'] = "Feedback submitted successfully!";
    header("Location: passenger_dashboard.php");
    exit();
}

