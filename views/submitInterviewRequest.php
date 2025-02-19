<?php
// Include database connection
global $db;
include('../config/Database.php');

// Check if the user is logged in (session check for user_id)
session_start();
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if user is not logged in
    header("Location: login.php");
    exit();
}

// Get the user_id from the session
$user_id = $_SESSION['user_id'];

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the data from the form
    $driver_id = $_POST['driver_id'];
    $interview_date = $_POST['interview_date'];
    $message = $_POST['message'];

    // Query to insert the interview request into the database
    $query = "INSERT INTO interview_requests (driver_id, user_id, interview_date, message) VALUES (?, ?, ?, ?)";

    if ($stmt = $db->prepare($query)) {
        // Bind the parameters
        $stmt->bind_param("iiss", $driver_id, $user_id, $interview_date, $message);

        // Execute the query
        if ($stmt->execute()) {
            echo "Interview request sent successfully!";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $db->error;
    }
}

