<?php
global $db;
session_start();
include_once '../config/Database.php'; // Include your database connection

// Check if the reservation ID is provided in the URL
if (isset($_GET['id'])) {
    $reservation_id = intval($_GET['id']); // Sanitize the input

    // Retrieve the user's session ID
    $user_id = $_SESSION['user_id'];

    // Verify if the reservation exists and belongs to the logged-in user
    $sqlCheck = "SELECT driver_id, passenger FROM reservations WHERE reservation_id = ? AND user_id = ?";
    $stmtCheck = $db->prepare($sqlCheck);
    $stmtCheck->bind_param("ii", $reservation_id, $user_id);
    $stmtCheck->execute();
    $result = $stmtCheck->get_result();

    if ($row = $result->fetch_assoc()) {
        $driver_id = $row['driver_id'];
        $passenger_count = $row['passenger'];

        // Add back the passengers to the driver's remaining seats
        $sqlUpdateSeats = "UPDATE drivers SET remaining_seats = remaining_seats + ? WHERE driver_id = ?";
        $stmtUpdateSeats = $db->prepare($sqlUpdateSeats);
        $stmtUpdateSeats->bind_param("ii", $passenger_count, $driver_id);
        $stmtUpdateSeats->execute();
        $stmtUpdateSeats->close();

        // Delete the reservation
        $sqlCancel = "DELETE FROM reservations WHERE reservation_id = ?";
        $stmtCancel = $db->prepare($sqlCancel);
        $stmtCancel->bind_param("i", $reservation_id);

        if ($stmtCancel->execute()) {
            $_SESSION['success_message'] = "Your reservation has been successfully canceled. The seats have been restored.";
        } else {
            $_SESSION['error_message'] = "Error: Unable to cancel the reservation. Please try again.";
        }

        $stmtCancel->close();
    } else {
        $_SESSION['error_message'] = "Reservation not found or you do not have permission to cancel it.";
    }

    $stmtCheck->close();
    $db->close();
    // Redirect back to reservation.php
    header("Location: reservation.php");
    exit();
} else {
    $_SESSION['error_message'] = "Invalid request. Reservation ID is missing.";
    header("Location: reservation.php");
    exit();
}

