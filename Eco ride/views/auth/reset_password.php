<?php

global $pdo;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include "Database.php";
    $token = $_GET['token'];
    $newPassword = $_POST['password'];

    // Validate token and check expiration
    $stmt = $pdo->prepare("SELECT * FROM users WHERE reset_token = :token AND token_expiration > NOW()");
    $stmt->execute([':token' => $token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Hash the new password
        $newPasswordHash = password_hash($newPassword, PASSWORD_BCRYPT);

        // Update the password in the database
        $stmt = $pdo->prepare("UPDATE users SET password = :password, reset_token = NULL, token_expiration = NULL WHERE reset_token = :token");
        $stmt->execute([':password' => $newPasswordHash, ':token' => $token]);

        echo "Your password has been successfully reset.";
    } else {
        echo "Invalid or expired token.";
    }
}

