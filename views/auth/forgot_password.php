<?php
session_start();
include('../../config/Database.php');
global $db;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);

    // Check if email exists in the database
    $query = "SELECT id FROM users WHERE email = ?";
    if ($stmt = $db->prepare($query)) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            // Generate a random temporary password
            try {
                $temporaryPassword = bin2hex(random_bytes(4));
            } catch (\Random\RandomException $e) {

            } // Example: "a1b2c3d4"
            $hashedPassword = password_hash($temporaryPassword, PASSWORD_BCRYPT);

            // Update the user's password in the database
            $updateQuery = "UPDATE users SET password = ? WHERE email = ?";
            if ($updateStmt = $db->prepare($updateQuery)) {
                $updateStmt->bind_param("ss", $hashedPassword, $email);
                $updateStmt->execute();

                // Send the temporary password to the user's email
                $subject = "Temporary Password for Your Account";
                $message = "Your temporary password is: $temporaryPassword\n\nPlease log in with this password and change it immediately.";
                $headers = "From: noreply@yourwebsite.com";

                if (mail($email, $subject, $message, $headers)) {
                    $_SESSION['success'] = "A temporary password has been sent to your email.";
                    header('Location: ../loginPage.php');
                    exit();
                } else {
                    $_SESSION['error'] = "Failed to send the email. Please try again.";
                }
            } else {
                $_SESSION['error'] = "Database error: Unable to update password.";
            }
        } else {
            $_SESSION['error'] = "No account found with that email address.";
        }
    } else {
        $_SESSION['error'] = "Database error: " . $db->error;
    }

    header('Location: ../forgotPassword.html');
    exit();
}

