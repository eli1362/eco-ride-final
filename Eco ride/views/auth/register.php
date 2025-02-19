<?php


include_once "registerUsers.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['full_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Trim input values to prevent extra spaces
    $fullName = trim($name);
    $email = trim($email);
    $password = trim($password);

    // Basic validation for empty fields
    if (empty($fullName) || empty($email) || empty($password)) {
        die("All fields are required.");
    }

    // Validate Full Name (must be more than 3 characters and not contain numbers or special characters)
    if (strlen($fullName) < 3) {
        die("Full name must be at least 3 characters.");
    }
    if (!preg_match("/^[a-zA-Z\s]+$/", $fullName)) {
        die("Full name can only contain letters and spaces.");
    }

    // Validate Email (must follow email format)
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Please enter a valid email address.");
    }

    // Validate Password (must be at least 8 characters, contain a letter, a number, and a special character)
    if (strlen($password) < 8) {
        die("Password must be at least 8 characters.");
    }
    if (!preg_match("/[A-Za-z]/", $password) || !preg_match("/[0-9]/", $password) || !preg_match("/[\W_]/", $password)) {
        die("Password must contain at least one letter, one number, and one special character.");
    }

    // Hash the password securely
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Prepare and execute the SQL query to insert the data
    try {
        registerUsers::InsertUser($fullName,$email,$hashedPassword);
        echo "Registration successful!";
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) { // Duplicate entry error code
            echo "Email is already registered.";
        } else {
            echo "Error: " . $e->getMessage();
        }
    }
}