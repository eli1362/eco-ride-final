<?php
session_start();
include_once '../config/Database.php'; // Ensure database connection is established

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = "You must be logged in to make a reservation.";
    header("Location: login.php");
    exit();
}

global $db;
$user_id = $_SESSION['user_id'];

// Check if driver_id is passed
if (!isset($_GET['driver_id'])) {
    $_SESSION['error_message'] = "Invalid driver ID.";
    header("Location: reservation.php");
    exit();
}

$driver_id = $_GET['driver_id'];

// 1️⃣ **Find the Most Recent Non-Electric Reservation**
$stmtFind = $db->prepare("
    SELECT reservation_id 
    FROM reservations 
    WHERE user_id = ? 
    AND driver_id IN (SELECT driver_id FROM drivers WHERE eco_friendly = 0) 
    ORDER BY reservation_date DESC 
    LIMIT 1
");
$stmtFind->bind_param("i", $user_id);
$stmtFind->execute();
$resultFind = $stmtFind->get_result();

if ($row = $resultFind->fetch_assoc()) {
    $reservation_id = $row['reservation_id'];

    // 2️⃣ **Cancel Only the Most Recent Non-Electric Reservation**
    $stmtCancel = $db->prepare("DELETE FROM reservations WHERE reservation_id = ?");
    $stmtCancel->bind_param("i", $reservation_id);
    $stmtCancel->execute();

    if ($stmtCancel->affected_rows > 0) {
        $_SESSION['success_message'] = "Your previous non-electric driver reservation has been canceled.";
    }
}

// 3️⃣ **Check if the New Electric Driver Exists and is Available**
$stmtCheck = $db->prepare("SELECT * FROM drivers WHERE driver_id = ? AND eco_friendly = 1");
$stmtCheck->bind_param("i", $driver_id);
$stmtCheck->execute();
$resultCheck = $stmtCheck->get_result();

if ($resultCheck->num_rows > 0) {
    $driver = $resultCheck->fetch_assoc();

    // 4️⃣ **Reserve the New Electric Driver with Full Details**
    $stmtReserve = $db->prepare("
        INSERT INTO reservations (driver_id, user_id, driver_name, driver_price, departure_time, date, reservation_date) 
        VALUES (?, ?, ?, ?, ?, ?, NOW())
    ");
    $stmtReserve->bind_param(
        "iisdss",
        $driver_id,
        $user_id,
        $driver['name'],
        $driver['price'],
        $driver['departure_time'],
        $driver['date']
    );

    if ($stmtReserve->execute()) {
        $_SESSION['success_message'] = "You have successfully reserved the electric driver: " . htmlspecialchars($driver['name']);
    } else {
        $_SESSION['error_message'] = "Reservation failed: " . $stmtReserve->error;
    }
} else {
    $_SESSION['error_message'] = "This electric driver is not available.";
}

// Redirect back to reservation page
header("Location: reservation.php");
exit();