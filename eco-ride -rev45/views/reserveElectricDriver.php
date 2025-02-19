<?php
session_start();
include_once '../config/Database.php';

// Ensure user is logged in
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

$driver_id = intval($_GET['driver_id']); // Ensure driver_id is an integer
$passenger = isset($_POST['passenger']) ? intval($_POST['passenger']) : 1; // Default to 1

// **Step 1: Check if the new driver is electric**
$stmtCheck = $db->prepare("SELECT * FROM drivers WHERE driver_id = ?");
$stmtCheck->bind_param("i", $driver_id);
$stmtCheck->execute();
$resultCheck = $stmtCheck->get_result();

if ($resultCheck->num_rows > 0) {
    $driver = $resultCheck->fetch_assoc();

    // **Check if the selected driver is electric**
    if ($driver['eco_friendly'] != 1) {
        $_SESSION['error_message'] = "Error: The selected driver is not electric!";
        header("Location: pollution.php"); // Redirect to pollution page
        exit();
    }

    // **Step 2: Cancel Previous Non-Electric Reservation & Get Passenger Count**
    $stmtFind = $db->prepare("
        SELECT reservation_id, driver_id, passenger 
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
        $old_driver_id = $row['driver_id'];
        $passenger_count = $row['passenger']; // 🔹 Preserve previous passenger count
        $passenger = $passenger_count; // ✅ Ensure we keep the same number of passengers

        // **Cancel the Previous Reservation**
        $stmtCancel = $db->prepare("DELETE FROM reservations WHERE reservation_id = ?");
        $stmtCancel->bind_param("i", $reservation_id);
        $stmtCancel->execute();

        if ($stmtCancel->affected_rows > 0) {
            // **Restore the old driver's available seats**
            $stmtUpdateSeats = $db->prepare("UPDATE drivers SET remaining_seats = remaining_seats + ? WHERE driver_id = ?");
            $stmtUpdateSeats->bind_param("ii", $passenger_count, $old_driver_id);
            $stmtUpdateSeats->execute();

            // **Reduce user's credits by 2**
            $stmtUpdateCredits = $db->prepare("UPDATE users SET credits = credits - 2 WHERE user_id = ?");
            $stmtUpdateCredits->bind_param("i", $user_id);
            $stmtUpdateCredits->execute();

            $_SESSION['success_message'] = "Your previous non-electric driver reservation has been canceled.";
        } else {
            $_SESSION['error_message'] = "Error canceling the previous reservation.";
            header("Location: reservation.php");
            exit();
        }
    }

    // **Step 3: Check if the New Electric Driver has Enough Seats**
    if ($driver['remaining_seats'] < $passenger) {
        $_SESSION['error_message'] = "Sorry, not enough available seats.";
        header("Location: reservation.php");
        exit();
    }

    // Set reservation details
    $driver_name = $driver['name'];
    $driver_price = $driver['price'];
    $departure_time = $driver['departure_time'];
    $reservation_date = !empty($driver['date']) ? $driver['date'] : date('Y-m-d');
    $eco_friendly = 1;

    // **Step 4: Insert the New Reservation with Correct Passenger Count**
    $stmtReserve = $db->prepare("
        INSERT INTO reservations (user_id, driver_id, driver_name, driver_price, departure_time, date, eco_friendly, passenger, reservation_date) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    $stmtReserve->bind_param(
        "iisdssii",
        $user_id,
        $driver_id,
        $driver_name,
        $driver_price,
        $departure_time,
        $reservation_date,
        $eco_friendly,
        $passenger
    );

    if ($stmtReserve->execute()) {
        // **Step 5: Update Credits and Driver's Seats**
        $stmtUpdateCredits = $db->prepare("UPDATE users SET credits = credits + 5 WHERE user_id = ?");
        $stmtUpdateCredits->bind_param("i", $user_id);
        $stmtUpdateCredits->execute();

        $stmtUpdateSeats = $db->prepare("UPDATE drivers SET remaining_seats = remaining_seats - ? WHERE driver_id = ?");
        $stmtUpdateSeats->bind_param("ii", $passenger, $driver_id);
        $stmtUpdateSeats->execute();

        $_SESSION['success_message'] = "Reservation successful! You earned 5 credits.";
        header("Location: reservation.php"); // ✅ Redirect to reservation.php
        exit();
    } else {
        $_SESSION['error_message'] = "Reservation failed: " . $stmtReserve->error;
        header("Location: reservation.php");
        exit();
    }
} else {
    $_SESSION['error_message'] = "This driver does not exist.";
    header("Location: reservation.php");
    exit();
}

