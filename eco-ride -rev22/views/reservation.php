<?php
global $db;
session_start();
include_once '../config/Database.php'; // Adjust the path to your database configuration file

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Error: You must be logged in to make a reservation.");
}

$user_id = $_SESSION['user_id'];

// Validate the POST data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $driver_id = isset($_POST['driver_id']) ? (int)$_POST['driver_id'] : 0;
    $passenger = isset($_POST['passenger']) ? (int)$_POST['passenger'] : 0;

    // Additional driver details from the form
    $driver_name = isset($_POST['driver_name']) ? $_POST['driver_name'] : "";
    $driver_price = isset($_POST['driver_price']) ? $_POST['driver_price'] : "";
    $departure_time = isset($_POST['departure_time']) ? $_POST['departure_time'] : "";
    $date = isset($_POST['date']) ? $_POST['date'] : "";
    $eco_friendly = isset($_POST['eco_friendly']) ? (int)$_POST['eco_friendly'] : 0; // 0 = No, 1 = Yes

    // Validate required fields
    if ($driver_id <= 0 || $passenger <= 0) {
        die("Error: Invalid driver ID or passenger count.");
    }
    if (empty($driver_name) || empty($driver_price) || empty($departure_time) || empty($date)) {
        die("Error: Missing driver details.");
    }

    // Check if the driver exists and has enough available seats
    $sqlCheckDriver = "SELECT * FROM drivers WHERE driver_id = ? AND remaining_seats >= ?";
    $stmtCheckDriver = $db->prepare($sqlCheckDriver);
    $stmtCheckDriver->bind_param("ii", $driver_id, $passenger);
    $stmtCheckDriver->execute();
    $resultCheckDriver = $stmtCheckDriver->get_result();

    if ($resultCheckDriver->num_rows === 0) {
        die("Error: The selected driver does not exist or does not have enough available seats.");
    }

    // Assign credits based on eco-friendliness
    $credits = $eco_friendly ? 5 : 2;

    // Save the reservation to the database
    $sqlInsertReservation = "
        INSERT INTO reservations (user_id, driver_id, driver_name, driver_price, departure_time, date, eco_friendly, passenger, reservation_date)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ";
    $stmtInsertReservation = $db->prepare($sqlInsertReservation);
    $stmtInsertReservation->bind_param("iissssii", $user_id, $driver_id, $driver_name, $driver_price, $departure_time, $date, $eco_friendly, $passenger);

    if ($stmtInsertReservation->execute()) {
        $reservation_id = $db->insert_id;
        // Update the driver's remaining seats
        $sqlUpdateSeats = "UPDATE drivers SET remaining_seats = remaining_seats - ? WHERE driver_id = ?";
        $stmtUpdateSeats = $db->prepare($sqlUpdateSeats);
        $stmtUpdateSeats->bind_param("ii", $passenger, $driver_id);
        $stmtUpdateSeats->execute();

        // Update the user's credits
        $sqlUpdateCredits = "UPDATE users SET credits = credits + ? WHERE user_id = ?";
        $stmtUpdateCredits = $db->prepare($sqlUpdateCredits);
        $stmtUpdateCredits->bind_param("ii", $credits, $user_id);
        $stmtUpdateCredits->execute();


        $message = "Success: Your reservation has been made! You have earned $credits credits.";
        $messageType = "success";


    } else {
        $message = "Error: Unable to save the reservation. Please try again.";
        $messageType = "error";
    }

    // Close all statements
    $stmtCheckDriver->close();
    $stmtInsertReservation->close();
    $stmtUpdateSeats->close();
    $stmtUpdateCredits->close();

} else {
    die("Error: Invalid request method.");
}

// Close the database connection
$db->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Process Your Reservation</title>
    <link rel="stylesheet" href="../public/assets/styles/app.css">
    <style>
        .success {
            color: green;
            font-weight: bold;
            margin: 10px 0;
        }

        .error {
            color: red;
            font-weight: bold;
            margin: 10px 0;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Process Your Reservation</h1>

    <!-- Display success or error message -->
    <?php if (isset($message)) : ?>
        <div class="<?= htmlspecialchars($messageType) ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <h3>You reserve the driver with the name: <?php echo htmlspecialchars($driver_name); ?>
        with the number of <?php echo htmlspecialchars($passenger); ?> passengers
        on the date of <?php echo htmlspecialchars($date); ?>
        at the time of <?php echo htmlspecialchars($departure_time); ?>.
        If for any reason you don't want it, you can cancel your travel by clicking this button.
    </h3>

    <!-- Dynamically include the reservation ID -->

    <a href="cancelReservation.php?id=<?= htmlspecialchars($reservation_id); ?>" style="color: red">Cancel</a>
</div>
</body>
</html>
