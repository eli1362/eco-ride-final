<?php
global $db;
session_start();

// Include database connection
include('../../config/Database.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitize inputs
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm-password'];
    $credits = 0; // Default credits
    $role_id = 2; // Assuming role_id for "User" is 2. Adjust according to your roles table.

    $errors = [];
    $old = [
        'full_name' => $full_name,
        'email' => $email
    ];

    // Validation
    if (empty($full_name)) {
        $errors['full_name'] = "Full name is required.";
    } elseif (strlen($full_name) < 3 || preg_match('/[^a-zA-Z\s]/', $full_name)) {
        $errors['full_name'] = "Full name must be at least 3 characters and contain only letters and spaces.";
    }

    if (empty($email)) {
        $errors['email'] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format.";
    }

    if (empty($password)) {
        $errors['password'] = "Password is required.";
    } elseif (strlen($password) < 8 || !preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password) || !preg_match('/[\W_]/', $password)) {
        $errors['password'] = "Password must be at least 8 characters with one letter, one number, and one special character.";
    }

    if ($password !== $confirm_password) {
        $errors['confirm_password'] = "Passwords do not match.";
    }

    if (empty($errors)) {
        // Check if email exists
        $query = "SELECT user_id FROM users WHERE email = ?";
        if ($stmt = $db->prepare($query)) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $_SESSION['errors']['email'] = "Email is already registered.";
                $_SESSION['old'] = $old;
                header("Location: ../registerPage.php");
                exit();
            }
        }

        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Check if role_id exists in roles table
        $role_check_query = "SELECT role_id FROM roles WHERE role_id = ?";
        if ($role_stmt = $db->prepare($role_check_query)) {
            $role_stmt->bind_param("i", $role_id);
            $role_stmt->execute();
            $role_stmt->store_result();
            if ($role_stmt->num_rows === 0) {
                $_SESSION['errors']['role'] = "Invalid role selected.";
                $_SESSION['old'] = $old;
                header("Location: ../registerPage.php");
                exit();
            }
        }

        // Insert user into database
        $query = "INSERT INTO users (full_name, email, password, credits, role_id) VALUES (?, ?, ?, ?, ?)";
        if ($stmt = $db->prepare($query)) {
            $stmt->bind_param("sssis", $full_name, $email, $hashed_password, $credits, $role_id);
            if ($stmt->execute()) {
                $_SESSION['message'] = "Registration successful. Please login.";
                header("Location: ../loginPage.php");
                exit();
            } else {
                $_SESSION['errors']['database'] = "Database error: " . $stmt->error;
            }
        } else {
            $_SESSION['errors']['database'] = "Database error: " . $db->error;
        }
    }

    // Save errors and old input data in session and redirect back
    $_SESSION['errors'] = $errors;
    $_SESSION['old'] = $old;
    header("Location: ../registerPage.php");
    exit();
}

