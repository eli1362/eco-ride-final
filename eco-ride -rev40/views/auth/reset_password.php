<?php
session_start();
include('../../config/Database.php');
global $db;

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "You need to be logged in to reset your password.";
    header('Location: ../loginPage.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];
    $currentPassword = trim($_POST['current_password']);
    $newPassword = trim($_POST['new_password']);
    $confirmPassword = trim($_POST['confirm_password']);

    // Validate new password
    if ($newPassword !== $confirmPassword) {
        $_SESSION['error'] = "Passwords do not match.";
        header('Location: ../resetPassword.php');
        exit();
    }

    if (strlen($newPassword) < 8 || !preg_match('/[A-Za-z]/', $newPassword) || !preg_match('/[0-9]/', $newPassword) || !preg_match('/[\W_]/', $newPassword)) {
        $_SESSION['error'] = "Password must be at least 8 characters and include letters, numbers, and special characters.";
        header('Location: ../resetPassword.php');
        exit();
    }

    // Fetch the current hashed password from the database
    $query = "SELECT password FROM users WHERE id = ?";
    if ($stmt = $db->prepare($query)) {
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Verify the current password
            if (!password_verify($currentPassword, $user['password'])) {
                $_SESSION['error'] = "Incorrect current password.";
                header('Location: ../resetPassword.php');
                exit();
            }

            // Update to the new password
            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
            $updateQuery = "UPDATE users SET password = ? WHERE id = ?";
            if ($updateStmt = $db->prepare($updateQuery)) {
                $updateStmt->bind_param("si", $hashedPassword, $userId);
                $updateStmt->execute();

                $_SESSION['success'] = "Your password has been successfully reset.";
                header('Location: ../loginPage.php');
                exit();
            } else {
                $_SESSION['error'] = "Database error: Unable to reset password.";
            }
        } else {
            $_SESSION['error'] = "User not found.";
        }
    } else {
        $_SESSION['error'] = "Database error: " . $db->error;
    }

    header('Location: ../resetPassword.php');
    exit();
}

