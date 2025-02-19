<?php
global $pdo;
session_start();  // Start the session

// Include database connection
include "Database.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = $_POST['email'];
    $password = $_POST['password'];

    // Trim the input to remove unwanted spaces
    $email = trim($email);
    $password = trim($password);

    // Check if both fields are filled
    if (empty($email) || empty($password)) {
        echo "Both email and password are required.";
        exit;
    }

    try {
        // Query to check if user exists by email
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // User exists, now verify password
            if (password_verify($password, $user['password'])) {
                // Password is correct, create session for the user
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['email'] = $user['email'];

                // Handle Remember Me functionality
                if (isset($_POST['remember-me'])) {
                    setcookie('user_id', $user['id'], time() + (86400 * 30), "/");  // 30 days
                    setcookie('user_email', $user['email'], time() + (86400 * 30), "/");  // 30 days
                }

                // Redirect to the user's dashboard or homepage
                header("Location: dashboard.php");
            } else {
                echo "Incorrect password.";
            }
        } else {
            echo "User not found. Please register.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

