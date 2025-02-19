<?php
// Enable error reporting for debugging
global $db;
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection details
include '../../config/Database.php'; // Include your database connection file

// Check the connection
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $depart = htmlspecialchars(trim($_POST['depart']));
    $destination = htmlspecialchars(trim($_POST['destination']));
    $date = htmlspecialchars(trim($_POST['date']));
    $passenger = intval($_POST['passenger']);
    $carType = htmlspecialchars(trim($_POST['carType']));
    $time = htmlspecialchars(trim($_POST['time']));

    // Validate input
    if (empty($depart) || empty($destination) || empty($date) || empty($time) || empty($carType)) {
        echo "All fields are required.";
        exit();
    }

    // Search for drivers matching the criteria
    $stmt = $db->prepare("
        SELECT * FROM drivers 
        WHERE date = ? 
        AND remaining_seats >= ? 
        AND eco_friendly = ? 
        ORDER BY departure_time
    ");

    $isEcoFriendly = ($carType === 'Electric') ? 1 : 0;
    $stmt->bind_param("sii", $date, $passenger, $isEcoFriendly);
    $stmt->execute();
    $result = $stmt->get_result();
    $drivers = $result->fetch_all(MYSQLI_ASSOC);

    // If no exact matches, suggest nearby drivers
    if (empty($drivers)) {
        $stmt = $db->prepare("
            SELECT * FROM drivers 
            WHERE remaining_seats >= ? 
            AND eco_friendly = ? 
            AND ABS(TIMESTAMPDIFF(DAY, date, ?)) <= 3
            ORDER BY ABS(TIMESTAMPDIFF(DAY, date, ?)), departure_time
        ");
        $stmt->bind_param("iiss", $passenger, $isEcoFriendly, $date, $date);
        $stmt->execute();
        $result = $stmt->get_result();
        $drivers = $result->fetch_all(MYSQLI_ASSOC);
    }

    // Check if there are available drivers
    if (!empty($drivers)) {
        // Display available drivers for user to select
        echo "<h2>Available Drivers</h2>";
        foreach ($drivers as $driver) {
            echo "<div>";
            echo "<p>Driver Name: " . htmlspecialchars($driver['name']) . "</p>";
            echo "<p>Departure: " . $driver['date'] . ' at ' . $driver['departure_time'] . "</p>";
            echo "<p>Arrival: " . $driver['arrival_time'] . "</p>";
            echo "<p>Seats Available: " . $driver['remaining_seats'] . "</p>";
            echo "<p>Eco-Friendly: " . ($driver['eco_friendly'] ? "Yes" : "No") . "</p>";
            echo "<form action='../reserveDriver.php' method='POST'>";
            echo "<input type='hidden' name='driver_id' value='" . $driver['id'] . "'>";
            echo "<input type='hidden' name='depart' value='" . htmlspecialchars($depart) . "'>";
            echo "<input type='hidden' name='destination' value='" . htmlspecialchars($destination) . "'>";
            echo "<input type='hidden' name='date' value='" . htmlspecialchars($date) . "'>";
            echo "<input type='hidden' name='time' value='" . htmlspecialchars($time) . "'>";
            echo "<input type='hidden' name='passenger' value='" . htmlspecialchars($passenger) . "'>";
            echo "<input type='hidden' name='car_type' value='" . htmlspecialchars($carType) . "'>";
            echo "<button type='submit'>Reserve</button>";
            echo "</form>";
            echo "</div>";
        }
    } else {
        echo "No drivers are available for the selected criteria. Please try another search.";
    }

    // Close the statement
    $stmt->close();
    $db->close();
} else {
    echo "Invalid request method.";
}

