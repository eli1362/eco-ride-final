<?php
global $db;
session_start();
include '../../config/Database.php'; // Include your database connection file

// Initialize error messages
$errors = [
    'email' => '',
    'password' => ''
];

// Initialize old input values
$old = [
    'email' => ''
];

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $old['email'] = $email; // Preserve the email input

    // Validate email and password
    if (empty($email)) {
        $errors['email'] = 'Email is required';
    } elseif (empty($password)) {
        $errors['password'] = 'Password is required';
    } else {
        // Check if email exists in the database
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user) {
            // Check password
            if (password_verify($password, $user['password'])) {
                // Successful login
                $_SESSION['user'] = $user;
                header("Location: userPage.php");
                exit();
            } else {
                // Incorrect password
                $errors['password'] = 'Your password is not correct';
            }
        } else {
            // Email not registered
            $errors['email'] = 'You are not registered. Please register first.';
        }
    }
}
?>