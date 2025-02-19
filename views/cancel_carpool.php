<?php
global $db;
session_start();
include_once "../config/Database.php";
include_once "../config/Mail.php"; // Include the Mail class

// Check if the user is logged in
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

    // Extract carpool details
    $passenger_id = $carpool['user_id'];
    $passenger_name = $carpool['user_name'];
    $departure_date = $carpool['departure_date'];
    $departure_time = $carpool['departure_time'];
    $price = $carpool['price']; // Price for the reservation
    $eco_friendly = $carpool['eco_friendly']; // Eco-friendly status of the driver (yes/no)

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

    // 1. Update the driver's remaining seats based on the number of passengers in the canceled carpool
    $passenger_count = $carpool['remaining_seats']; // Assuming remaining_seats represents the number of passengers

    $driver_sql = "UPDATE drivers SET remaining_seats = remaining_seats + ? WHERE driver_id = ?";
    $driver_stmt = $db->prepare($driver_sql);
    $driver_stmt->bind_param("ii", $passenger_count, $carpool['driver_id']);
    $driver_stmt->execute();
    $driver_stmt->close();

    // 2. Subtract credits from the user's account if they paid for the reservation
    if ($price > 0) {
        $credit_amount = ($eco_friendly == 'yes') ? 5 : 2; // Eco-friendly drivers get 5 credits, others get 2

        $credit_sql = "UPDATE users SET credits = credits - ? WHERE user_id = ?";
        $credit_stmt = $db->prepare($credit_sql);
        $credit_stmt->bind_param("di", $credit_amount, $passenger_id);
        $credit_stmt->execute();
        $credit_stmt->close();
    }

    // Delete the carpool entry from the database
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

        // Send email and check if it was successful
        $email_status = $mail->sendMail(null, $subject, $message, $db);
        if (strpos($email_status, "successfully sent") !== false) {
            $_SESSION['status_message'] = "Covoiturage annulé. " . $email_status;
        } else {
            $_SESSION['error_message'] = "Covoiturage annulé, mais l'email n'a pas été envoyé. " . $email_status;
        }

        // Find an alternative driver
        $alternative_sql = "
            SELECT * FROM drivers
            WHERE driver_id != ? AND DATE(date) = ? AND TIME(departure_time) = ?  
            LIMIT 1
        ";
        $alternative_stmt = $db->prepare($alternative_sql);
        $alternative_stmt->bind_param("iss", $carpool['driver_id'], $departure_date, $departure_time);
        $alternative_stmt->execute();
        $alternative_result = $alternative_stmt->get_result();

        if ($alternative_driver = $alternative_result->fetch_assoc()) {
            $_SESSION['status_message'] = "Covoiturage annulé. Nous avons trouvé une alternative avec le conducteur {$alternative_driver['user_name']}.";
        } else {
            $_SESSION['status_message'] = "Covoiturage annulé. Aucun autre conducteur disponible pour ce créneau.";
        }

        $alternative_stmt->close();
    } else {
        $_SESSION['error_message'] = "Erreur lors de l'annulation du covoiturage.";
    }

    // Redirect to history-carpool.php
    header("Location: history-carpool.php");
    exit();
}
