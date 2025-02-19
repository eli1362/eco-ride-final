<?php
global $db;
session_start();
include_once '../config/Database.php';

// Check if driver ID is passed in the query string
if (isset($_GET['driver_id'])) {
    $driver_id = $_GET['driver_id'];
    $user_id = $_SESSION['user_id']; // Assuming the user is logged in

    // First, cancel any previous non-electric reservation (if any)
    // I assume "status" column exists and 'reserved' indicates the reservation status
    $stmtCancel = $db->prepare("DELETE FROM reservations WHERE user_id = ? AND driver_id IN (SELECT driver_id FROM drivers WHERE eco_friendly = 0) AND status = 'reserved'");
    $stmtCancel->bind_param("i", $user_id);
    $stmtCancel->execute();

    // Now, reserve the new electric driver
    $stmt = $db->prepare("SELECT * FROM drivers WHERE driver_id = ? AND eco_friendly = 1"); // Assuming the correct column is driver_id
    $stmt->bind_param("i", $driver_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Driver exists and is eco-friendly, proceed with reservation
        $driver = $result->fetch_assoc();

        // Insert the reservation for the electric driver
        $stmtReserve = $db->prepare("INSERT INTO reservations (driver_id, user_id, reservation_date, status) VALUES (?, ?, NOW(), 'reserved')");
        $stmtReserve->bind_param("ii", $driver_id, $user_id);
        $stmtReserve->execute();

        // Set a success message
        $_SESSION['success_message'] = "You have successfully reserved the driver: " . htmlspecialchars($driver['name']);
        header("Location: reservation.php"); // Redirect back to the reservation page
        exit();
    } else {
        // Driver not found or not available
        $_SESSION['error_message'] = "This driver is not available.";
        header("Location: reservation.php"); // Redirect back to the reservation page
        exit();
    }
} else {
    $_SESSION['error_message'] = "Invalid driver ID.";
    header("Location: reservation.php");
    exit();
}
