<?php
// Include database connection
global $db;
include('../config/Database.php');

// Check if reservation_id is passed in the URL
if (isset($_GET['reservation_id'])) {
    $reservation_id = $_GET['reservation_id'];

    // Fetch reservation details from the database
    $query = "SELECT r.driver_name, r.driver_price, r.departure_time, r.date, r.eco_friendly, u.name AS user_name
              FROM reservations r
              JOIN users u ON r.user_id = u.id
              WHERE r.id = ?";

    $stmt = $db->prepare($query);
    $stmt->bind_param("i", $reservation_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Fetch the reservation details
        $reservation = $result->fetch_assoc();
    } else {
        echo "No reservation found.";
        exit;
    }

    // Close the statement
    $stmt->close();
} else {
    echo "Reservation ID not provided.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation Confirmation</title>
</head>
<body>

<h1>Reservation Confirmation</h1>

<p>Thank you for your reservation, <?php echo htmlspecialchars($reservation['user_name']); ?>!</p>

<h2>Your Reservation Details:</h2>
<ul>
    <li><strong>Driver:</strong> <?php echo htmlspecialchars($reservation['driver_name']); ?></li>
    <li><strong>Price:</strong> $<?php echo htmlspecialchars($reservation['driver_price']); ?></li>
    <li><strong>Departure Time:</strong> <?php echo htmlspecialchars($reservation['departure_time']); ?></li>
    <li><strong>Date:</strong> <?php echo htmlspecialchars($reservation['date']); ?></li>
    <li><strong>Eco-Friendly Ride:</strong> <?php echo ($reservation['eco_friendly'] ? "Yes" : "No"); ?></li>
</ul>

<p>Your reservation has been successfully processed.</p>

</body>
</html>


