<?php
global $db;
session_start();
include_once '../config/Database.php';

if (isset($_GET['user_id']) && !empty($_GET['user_id'])) {
    $id = $_GET['user_id'];
    $query = "DELETE FROM users WHERE user_id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("i", $id);
} elseif (isset($_GET['driver_id']) && !empty($_GET['driver_id'])) {
    $id = $_GET['driver_id'];
    $query = "DELETE FROM drivers WHERE driver_id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("i", $id);
} else {
    $_SESSION['error_message'] = "Invalid request. No user or driver ID provided.";
    header("Location: view_users_employees.php");
    exit();
}

if ($stmt->execute()) {
    $_SESSION['success_message'] = "User/Driver deleted successfully.";
} else {
    $_SESSION['error_message'] = "Error occurred while deleting the user/driver: " . $stmt->error;
}

// Redirect back to the user management page
header("Location: view_users_employees.php");
exit();
