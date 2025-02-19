<?php
session_start();
include_once '../config/Database.php'; // Adjust the path to your database configuration file

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Error: You must be logged in to view your reservation history.");
}

$user_id = $_SESSION['user_id'];

// Fetch the reservation history for the logged-in user
$sqlFetchHistory = "
    SELECT r.driver_name, r.driver_price, r.departure_time, r.date, r.eco_friendly, r.passenger, r.reservation_date
    FROM reservations r
    WHERE r.user_id = ?
    ORDER BY r.reservation_date DESC
";
$stmtFetchHistory = $db->prepare($sqlFetchHistory);
$stmtFetchHistory->bind_param("i", $user_id);
$stmtFetchHistory->execute();
$resultHistory = $stmtFetchHistory->get_result();

// Check if there are any reservations
if ($resultHistory->num_rows === 0) {
    echo "<p>You have no reservation history.</p>";
} else {
    echo "<h1>Your Reservation History</h1>";
    echo "<table border='1'>";
    echo "<tr>
        <th>Driver Name</th>
        <th>Price</th>
        <th>Departure Time</th>
        <th>Date</th>
        <th>Eco-Friendly</th>
        <th>Passengers</th>
        <th>Credits Earned</th>
        <th>Reservation Date</th>
    </tr>";

    while ($row = $resultHistory->fetch_assoc()) {
        // Calculate credits
        $credits = $row['eco_friendly'] ? 5 : 2;

        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['driver_name']) . "</td>";
        echo "<td>$" . htmlspecialchars($row['driver_price']) . "</td>";
        echo "<td>" . htmlspecialchars($row['departure_time']) . "</td>";
        echo "<td>" . htmlspecialchars($row['date']) . "</td>";
        echo "<td>" . ($row['eco_friendly'] ? "Yes" : "No") . "</td>";
        echo "<td>" . htmlspecialchars($row['passenger']) . "</td>";
        echo "<td>" . $credits . "</td>";
        echo "<td>" . htmlspecialchars($row['reservation_date']) . "</td>";
        echo "</tr>";
    }

    echo "</table>";
}

// Close the statement and database connection
$stmtFetchHistory->close();
$db->close();