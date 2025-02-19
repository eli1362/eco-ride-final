<?php
global $db;
session_start();
include_once '../config/Database.php'; // Adjust the path to your database config file

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['reservation_id'])) {
    $reservation_id = $_POST['reservation_id'];
    $driver_id = $_POST['driver_id'];  // Ensure that you are passing the driver ID as well
    $departure_time = $_POST['departure_time']; // Ensure you also have the departure time for matching

    // Connect to the database
    global $db;

    // Check the current status in the reservations table
    $sqlCheckStatus = "SELECT status FROM reservations WHERE reservation_id = ?";
    $stmtCheck = $db->prepare($sqlCheckStatus);
    $stmtCheck->bind_param("i", $reservation_id);
    $stmtCheck->execute();
    $resultCheck = $stmtCheck->get_result();
    $row = $resultCheck->fetch_assoc();
    $stmtCheck->close();

    if ($row) {
        $current_status = $row['status'];

        // Allow update if the status is 'pending' or 'started'
        if ($current_status === 'pending' || $current_status === 'started') {
            // Update status to "finished" in the reservations table
            $sqlUpdate = "UPDATE reservations SET status = 'finished' WHERE reservation_id = ?";
            $stmtUpdate = $db->prepare($sqlUpdate);
            $stmtUpdate->bind_param("i", $reservation_id);
            if ($stmtUpdate->execute()) {
                // After updating the reservations table, update the carpool status
                // Use driver_id and departure_time to find the correct carpool to update
                $sqlUpdateCarpool = "UPDATE carpool SET status = 'finished' WHERE driver_id = ? AND departure_time = ?";
                $stmtUpdateCarpool = $db->prepare($sqlUpdateCarpool);
                $stmtUpdateCarpool->bind_param("is", $driver_id, $departure_time); // Adjust depending on the data type
                if ($stmtUpdateCarpool->execute()) {
                    echo json_encode(["success" => true, "message" => "Le trajet est terminé et le covoiturage a été mis à jour."]);
                } else {
                    echo json_encode(["success" => false, "message" => "Erreur lors de la mise à jour du covoiturage."]);
                }
                $stmtUpdateCarpool->close();
            } else {
                echo json_encode(["success" => false, "message" => "Erreur lors de la mise à jour de la réservation."]);
            }
            $stmtUpdate->close();
        } else {
            echo json_encode(["success" => false, "message" => "Statut invalide."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Réservation introuvable."]);
    }

    $db->close();
}

