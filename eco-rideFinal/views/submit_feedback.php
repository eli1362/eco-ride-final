<?php
global $db, $reservation_id;
session_start();
include_once '../config/Database.php';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['reservation_id'], $_POST['rating'], $_POST['feedback'])) {
        $reservation_id = $_POST['reservation_id'];
        $rating = $_POST['rating'];
        $feedback = $_POST['feedback'];

        // Step 1: Insert feedback and rating into the `reservations` table
        $sqlInsertFeedback = "UPDATE reservations SET feedback = ?, rating = ? WHERE reservation_id = ?";
        $stmtInsertFeedback = $db->prepare($sqlInsertFeedback);
        $stmtInsertFeedback->bind_param("sii", $feedback, $rating, $reservation_id);

        if ($stmtInsertFeedback->execute()) {
            // Step 2: Get the driver_id from the reservations table
            $sqlGetDriverId = "SELECT driver_id FROM reservations WHERE reservation_id = ?";
            $stmtGetDriverId = $db->prepare($sqlGetDriverId);
            $stmtGetDriverId->bind_param("i", $reservation_id);
            $stmtGetDriverId->execute();
            $result = $stmtGetDriverId->get_result();
            $driver = $result->fetch_assoc();
            $driver_id = $driver['driver_id'];
            $stmtGetDriverId->close();

            // Step 3: Update the rating for the carpool table if applicable
            $sqlUpdateCarpoolRating = "UPDATE carpool SET rating = ? WHERE driver_id = ? AND status = 'finished'";
            $stmtUpdateCarpoolRating = $db->prepare($sqlUpdateCarpoolRating);
            $stmtUpdateCarpoolRating->bind_param("di", $rating, $driver_id);
            $stmtUpdateCarpoolRating->execute();
            $stmtUpdateCarpoolRating->close();

            // Step 4: Calculate the new average rating for the driver
            // Get all ratings for the driver from both tables
            $sqlGetRatings = "
                SELECT rating FROM reservations WHERE driver_id = ? AND rating IS NOT NULL
                UNION ALL
                SELECT rating FROM carpool WHERE driver_id = ? AND rating IS NOT NULL
            ";
            $stmtGetRatings = $db->prepare($sqlGetRatings);
            $stmtGetRatings->bind_param("ii", $driver_id, $driver_id);
            $stmtGetRatings->execute();
            $resultRatings = $stmtGetRatings->get_result();

            $totalRatings = 0;
            $ratingCount = 0;
            while ($row = $resultRatings->fetch_assoc()) {
                $totalRatings += $row['rating'];
                $ratingCount++;
            }
            $stmtGetRatings->close();

            if ($ratingCount > 0) {
                $averageRating = $totalRatings / $ratingCount;
            } else {
                $averageRating = 0;
            }

            // Step 5: Update the driver's average rating in the `drivers` table
            $sqlUpdateDriverRating = "UPDATE drivers SET rating = ? WHERE driver_id = ?";
            $stmtUpdateDriverRating = $db->prepare($sqlUpdateDriverRating);
            $stmtUpdateDriverRating->bind_param("di", $averageRating, $driver_id);
            $stmtUpdateDriverRating->execute();
            $stmtUpdateDriverRating->close();

            // Step 6: Redirect to a success page or show success message
            $_SESSION['success_message'] = "Feedback submitted successfully!";
            // Redirect to history or dashboard page, or wherever you want
            header("Location: history.php");
            exit();
        } else {
            $_SESSION['error_message'] = "Error submitting feedback.";
            header("Location: feedback_form.php?reservation_id=$reservation_id");
            exit();
        }

        $stmtInsertFeedback->close();
    } else {
        $_SESSION['error_message'] = "Invalid input data.";
        header("Location: feedback_form.php?reservation_id=$reservation_id");
        exit();
    }
} else {
    $_SESSION['error_message'] = "Invalid request method.";
    header("Location: feedback_form.php");
    exit();
}