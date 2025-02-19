<?php
session_start();
include('../config/Database.php');

global $db;

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Collect form data
    $role_id = $_POST['role_id'];
    $phone = $_POST['phone'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $date = $_POST['date'];
    $departure_time = $_POST['departure_time'];
    $arrival_time = $_POST['arrival_time'];
    $carType = $_POST['carType'];
    $plate_number = $_POST['plate_number'];
    $registration_date = $_POST['registration_date'];
    $model = $_POST['model'];
    $color = $_POST['color'];
    $brand = $_POST['brand'];
    $remaining_seats = $_POST['remaining_seats'];
    $smoker = $_POST['smoker'];
    $animals = $_POST['animals'];
    $custom_preferences = $_POST['custom_preferences'];
    $rating = 1.0;

    // Check if the driver is also a user
    $user_id = isset($_SESSION['user_id']) && $role_id == 3 ? $_SESSION['user_id'] : NULL;


    // Handle photo upload
    $photo = "";
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $photo_name = $_FILES['photo']['name'];
        $photo_tmp = $_FILES['photo']['tmp_name'];
        $photo_extension = pathinfo($photo_name, PATHINFO_EXTENSION);
        $photo_name_new = time() . '.' . $photo_extension;
        $photo_upload_path = '../public/assets/images/' . $photo_name_new;

        if (move_uploaded_file($photo_tmp, $photo_upload_path)) {
            $photo = $photo_upload_path;
        } else {
            echo "Error uploading photo.";
            exit();
        }
    }

    // Set eco_friendly based on the carType
    $eco_friendly = ($carType == 'electric') ? 1 : 0;

    // Prepare and bind the SQL statement
    $stmt = $db->prepare("INSERT INTO drivers (name, photo, rating, remaining_seats, price, date, departure_time, arrival_time, eco_friendly, plate_number, registration_date, model, color, brand, smoker, animals, custom_preferences, user_id, role_id, phone) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param("ssdidsssisssssiisii", $name, $photo, $rating, $remaining_seats, $price, $date, $departure_time, $arrival_time, $eco_friendly, $plate_number, $registration_date, $model, $color, $brand, $smoker, $animals, $custom_preferences, $user_id, $role_id, $phone);


    // Execute the statement
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Your reservation has been successfully made!";
        header("Location: userPage1.php");
        exit();
    } else {
        $_SESSION['error_message'] = "Unable to save the reservation. Please try again.";
        header("Location: userPage1.php");
        exit();
    }

    // Close the prepared statement and the connection
    $stmt->close();
    $db->close();
}
