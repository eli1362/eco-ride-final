<?php
// Include the database configuration
global $db;
include '../config/Database.php';

session_start();  // Start session for user authentication

// Check if the user is logged in
//if (!isset($_SESSION['user_id'])) {
//    // Redirect to login page if user is not logged in
//    header('Location: loginPage.php');
//    exit();
//}

// Process the reservation when the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the driver ID from the form submission
    $driverId = intval($_POST['driver_id']);
    $userId = $_SESSION['user_id'];  // Get the user ID from session

    // Query to check the availability of the driver (seats left)
    $stmt = $db->prepare("SELECT remaining_seats, eco_friendly, price FROM drivers WHERE id = ?");
    $stmt->bind_param("i", $driverId);
    $stmt->execute();
    $result = $stmt->get_result();
    $driver = $result->fetch_assoc();

    // Check if the driver exists and has available seats
    if (!$driver || $driver['remaining_seats'] <= 0) {
        // If no available seats or driver not found, show an error message
        echo "Driver is no longer available.";
        exit();
    }

    // Proceed with the reservation (insert into reservations table)
    $stmt = $db->prepare("INSERT INTO reservations (user_id, driver_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $userId, $driverId);

    // Execute the reservation query
    if ($stmt->execute()) {
        // Update remaining seats for the driver after successful reservation
        $stmt = $db->prepare("UPDATE drivers SET remaining_seats = remaining_seats - 1 WHERE id = ?");
        $stmt->bind_param("i", $driverId);
        $stmt->execute();

        // Calculate and update the user's score (example logic)
        $newScore = 100;  // Placeholder starting score
        if ($driver['eco_friendly']) {
            $newScore += 20;  // Add bonus for eco-friendly driver
        }
        $newScore -= $driver['price'] / 10;  // Deduct score based on price (example logic)

        // Update the user's score in the users table
        $stmt = $db->prepare("UPDATE users SET score = ? WHERE id = ?");
        $stmt->bind_param("ii", $newScore, $userId);
        $stmt->execute();

        // Redirect to a confirmation page after reservation success
        header('Location: confirmationPage.php?reservation=success');
        exit();
    } else {
        // If there was an error with the reservation
        echo "Error: " . $stmt->error;
    }
}

