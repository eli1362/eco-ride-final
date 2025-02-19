<?php
session_start();
include_once '../config/Database.php'; // Adjust the path to your database config file

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['reservation_id'])) {
    $reservation_id = $_POST['reservation_id'];

    // Connect to the database
    global $db;

    // Check the current status
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
            // Update status to "finished"
            $sqlUpdate = "UPDATE reservations SET status = 'finished' WHERE reservation_id = ?";
            $stmtUpdate = $db->prepare($sqlUpdate);
            $stmtUpdate->bind_param("i", $reservation_id);
            if ($stmtUpdate->execute()) {
                echo json_encode(["success" => true, "message" => "Le trajet est terminé."]);
            } else {
                echo json_encode(["success" => false, "message" => "Erreur lors de la mise à jour."]);
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

