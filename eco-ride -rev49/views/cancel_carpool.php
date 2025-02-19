<?php
global $db;
session_start();
include_once "../config/Database.php";
include_once "../config/Mail.php"; // Include the Mail class

if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = "Vous devez être connecté pour annuler un covoiturage.";
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['carpool_id'])) {
    $carpool_id = $_POST['carpool_id'];

    // Ensure database connection is available
    if (!$db) {
        $_SESSION['error_message'] = "Erreur de connexion à la base de données.";
        header("Location: history-carpool.php");
        exit();
    }

    // Get the carpool details before deleting
    $sql = "SELECT * FROM carpool WHERE carpool_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $carpool_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $carpool = $result->fetch_assoc();
    $stmt->close();

    if (!$carpool) {
        $_SESSION['error_message'] = "Covoiturage introuvable.";
        header("Location: history-carpool.php");
        exit();
    }

    $passenger_id = $carpool['user_id'];
    $passenger_name = $carpool['user_name'];
    $departure_date = $carpool['departure_date'];
    $departure_time = $carpool['departure_time'];

    // Get the user's email
    $user_sql = "SELECT email FROM users WHERE user_id = ?";
    $user_stmt = $db->prepare($user_sql);
    $user_stmt->bind_param("i", $passenger_id);
    $user_stmt->execute();
    $user_result = $user_stmt->get_result();
    $user = $user_result->fetch_assoc();
    $user_stmt->close();

    $passenger_email = $user['email'] ?? '';

    // Check if email is found
    if (!$passenger_email) {
        $_SESSION['error_message'] = "Email de l'utilisateur introuvable.";
        header("Location: history-carpool.php");
        exit();
    }

    // Delete the carpool entry
    $delete_sql = "DELETE FROM carpool WHERE carpool_id = ?";
    $delete_stmt = $db->prepare($delete_sql);
    $delete_stmt->bind_param("i", $carpool_id);

    if ($delete_stmt->execute()) {
        $delete_stmt->close();

        // Initialize Mail class
        $mail = new Mail();

        // Send cancellation email
        $subject = "Annulation de votre covoiturage";
        $message = "
            Bonjour $passenger_name,<br><br>
            Votre covoiturage prévu le <b>$departure_date</b> à <b>$departure_time</b> a été annulé.<br>
            Nous vous proposons une alternative si disponible.<br><br>
            Merci,<br>L'équipe EcoRide.
        ";

        $email_status= $mail->sendMail(null, $subject, $message, $db);
        if (strpos($email_status, "successfully sent") !== false) {
            $_SESSION['success_message'] = "Covoiturage annulé. " . $email_status;
        } else {
            $_SESSION['error_message'] = "Covoiturage annulé, mais l'email n'a pas été envoyé. " . $email_status;
        }

        // Find an alternative driver
        $alternative_sql = "
    SELECT * FROM drivers
    WHERE driver_id != ?
    AND DATE(date) = ? 
    AND TIME(departure_time) = ?  
    LIMIT 1
        ";
        $alternative_stmt = $db->prepare($alternative_sql);
        $alternative_stmt->bind_param("iss", $carpool['driver_id'], $departure_date, $departure_time);
        $alternative_stmt->execute();
        $alternative_result = $alternative_stmt->get_result();

        if ($alternative_driver = $alternative_result->fetch_assoc()) {
            $_SESSION['success_message'] = "Covoiturage annulé. Nous avons trouvé une alternative avec le conducteur {$alternative_driver['user_name']}.";
        } else {
            $_SESSION['success_message'] = "Covoiturage annulé. Aucun autre conducteur disponible pour ce créneau.";
        }

        $alternative_stmt->close();

    } else {
        $_SESSION['error_message'] = "Erreur lors de l'annulation du covoiturage.";
    }

    header("Location: history-carpool.php");
    exit();
}

