<?php
global $pdo;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include "Database.php";
    $email = $_POST['email'];

    // Check if email exists in the database
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Generate a unique reset token
        try {
            $token = bin2hex(random_bytes(50));
        } catch (\Random\RandomException $e) {

        }

        // Save the token in the database (with expiration time)
        $stmt = $pdo->prepare("UPDATE users SET reset_token = :token, token_expiration = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE email = :email");
        $stmt->execute([':token' => $token, ':email' => $email]);

        // Send the reset email
        $resetLink = "https://yourwebsite.com/reset_password.php?token=$token";
        mail($email, "Password Reset Request", "Click on the following link to reset your password: $resetLink");

        echo "A password reset link has been sent to your email.";
    } else {
        echo "Email not found.";
    }
}

