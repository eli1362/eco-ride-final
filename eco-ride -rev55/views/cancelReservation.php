<?php
global $db;
session_start();
include_once '../config/Database.php'; // Include your database connection

// Check if the reservation ID is provided in the form
if (isset($_POST['reservation_id']) && !empty($_POST['reservation_id'])) {
    $reservation_id = intval($_POST['reservation_id']); // Sanitize the input

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

        // Retrieve Driver Information to Check Eco-Friendliness
        $sqlDriverCheck = "SELECT eco_friendly FROM drivers WHERE driver_id = ?";
        $stmtDriverCheck = $db->prepare($sqlDriverCheck);
        $stmtDriverCheck->bind_param("i", $driver_id);
        $stmtDriverCheck->execute();
        $resultDriver = $stmtDriverCheck->get_result();

        if ($driver = $resultDriver->fetch_assoc()) {
            $eco_friendly = $driver['eco_friendly'];

            // Add back the passengers to the driver's remaining seats
            $sqlUpdateSeats = "UPDATE drivers SET remaining_seats = remaining_seats + ? WHERE driver_id = ?";
            $stmtUpdateSeats = $db->prepare($sqlUpdateSeats);
            $stmtUpdateSeats->bind_param("ii", $passenger_count, $driver_id);
            $stmtUpdateSeats->execute();
            $stmtUpdateSeats->close();

            // Cancel the reservation
            $sqlCancel = "DELETE FROM reservations WHERE reservation_id = ?";
            $stmtCancel = $db->prepare($sqlCancel);
            $stmtCancel->bind_param("i", $reservation_id);

            if ($stmtCancel->execute()) {
                // Update the User's Credits
                if ($eco_friendly == 1) {
                    // Electric car, subtract 5 credits
                    $sqlUpdateCredits = "UPDATE users SET credits = credits - 5 WHERE user_id = ?";
                } else {
                    // Non-electric car, subtract 2 credits
                    $sqlUpdateCredits = "UPDATE users SET credits = credits - 2 WHERE user_id = ?";
                }
                $stmtUpdateCredits = $db->prepare($sqlUpdateCredits);
                $stmtUpdateCredits->bind_param("i", $user_id);
                $stmtUpdateCredits->execute();
                $stmtUpdateCredits->close();

                $_SESSION['success_message'] = "Your reservation has been successfully canceled. The seats have been restored, and your credits have been updated.";
            } else {
                $_SESSION['error_message'] = "Error: Unable to cancel the reservation. Please try again.";
            }

            $stmtCancel->close();
        } else {
            $_SESSION['error_message'] = "Driver not found or invalid.";
        }

        $stmtDriverCheck->close();
    } else {
        $_SESSION['error_message'] = "Reservation not found or you do not have permission to cancel it.";
    }

    $stmtCheck->close();
    $db->close();

    // Redirect back to the history page
    header("Location: history.php");
    exit();
} else {
    $_SESSION['error_message'] = "Invalid request. Reservation ID is missing.";
    header("Location: history.php");
    exit();
}
