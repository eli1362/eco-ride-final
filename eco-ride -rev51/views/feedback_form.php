<?php
session_start();
include_once "../config/Database.php";

if (!isset($_GET['carpool_id'])) {
    $_SESSION['error_message'] = "Invalid trip.";
    header("Location: passenger_dashboard.php");
    exit();
}

$carpool_id = $_GET['carpool_id'];
?>

<h2>Rate Your Trip</h2>
<form method="POST" action="submit_feedback.php">
    <input type="hidden" name="carpool_id" value="<?= $carpool_id ?>">

    <label for="rating">Rating (0-5):</label>
    <input type="number" name="rating" min="0" max="5" required>

    <label for="feedback">Feedback:</label>
    <textarea name="feedback" required></textarea>

    <button type="submit">Submit</button>
</form>

