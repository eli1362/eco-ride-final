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
    $sqlCheck = "SELECT * FROM reservations WHERE reservation_id = ? AND user_id = ?";
    $stmtCheck = $db->prepare($sqlCheck);
    $stmtCheck->bind_param("ii", $reservation_id, $user_id);
    $stmtCheck->execute();
    $result = $stmtCheck->get_result();

    if ($result->num_rows > 0) {
        // Reservation exists, proceed to cancel it
        $sqlCancel = "DELETE FROM reservations WHERE reservation_id = ?";
        $stmtCancel = $db->prepare($sqlCancel);
        $stmtCancel->bind_param("i", $reservation_id);

        if ($stmtCancel->execute()) {

            echo "Reservation successfully canceled.";
        } else {
            echo "Error: Unable to cancel the reservation. Please try again.";
        }

        $stmtCancel->close();
    } else {
        echo "Reservation not found or you do not have permission to cancel it.";
    }

    $stmtCheck->close();
    $db->close();
} else {
    // No ID provided in the URL
    die("Reservation ID is missing or invalid.");
}

