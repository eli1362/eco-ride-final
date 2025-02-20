<?php
global $db;
session_start();
include_once "../config/Database.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = "Vous devez être connecté pour voir votre historique de covoiturage.";
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['start_trip'])) {
        // Set status to 'started'
        $status = 'started';
        $carpool_id = $_POST['carpool_id'];

        // Prepare the SQL query to update the carpool status
        $sql = "UPDATE carpool SET status = ? WHERE carpool_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("si", $status, $carpool_id);

        if ($stmt->execute()) {
            $_SESSION['status_message'] = "Le covoiturage a démarré avec succès.";
            header('location:history-carpool.php');
        } else {
            $_SESSION['status_message'] = "Erreur : Impossible de démarrer le covoiturage.";
            header('location:history-carpool.php');
        }
        $stmt->close();
    }

    if (isset($_POST['finish_trip'])) {
        // Set status to 'finished'
        $status = 'finished';
        $carpool_id = $_POST['carpool_id'];

        // Prepare the SQL query to update the carpool status
        $sql = "UPDATE carpool SET status = ? WHERE carpool_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("si", $status, $carpool_id);

        if ($stmt->execute()) {
            $_SESSION['status_message'] = "Le covoiturage est terminé. Merci pour votre service.";
            header('location:history-carpool.php');

        } else {
            $_SESSION['status_message'] = "Erreur : Impossible de marquer le covoiturage comme terminé.";
            header('location:history-carpool.php');
        }
        $stmt->close();
    }

    if (isset($_POST['cancel_carpool'])) {
        $carpool_id = $_POST['carpool_id'];

        // Check if there are no available drivers for the time slot
        $check_sql = "SELECT * FROM carpool WHERE carpool_id = ? AND status = 'pending'";
        $check_stmt = $db->prepare($check_sql);
        $check_stmt->bind_param("i", $carpool_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            // Show message if no other driver is available
            $_SESSION['status_message'] = "Covoiturage annulé. Aucun autre conducteur disponible pour ce créneau.";
            header('location:history-carpool.php');
        } else {
            $_SESSION['status_message'] = "Le covoiturage a été annulé avec succès.";
            header('location:history-carpool.php');
        }

        $cancel_sql = "DELETE FROM carpool WHERE carpool_id = ?";
        $cancel_stmt = $db->prepare($cancel_sql);
        $cancel_stmt->bind_param("i", $carpool_id);
        $cancel_stmt->execute();
        $cancel_stmt->close();
    }
}

header("Location: history-carpool.php");
exit();
