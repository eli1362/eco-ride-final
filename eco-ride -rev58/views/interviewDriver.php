<?php

global $db;
include('../config/Database.php');

// Get the driver_id from the query string
if (isset($_GET['driver_id'])) {
    $driver_id = $_GET['driver_id'];

    // Query to get the driver details
    $query = "SELECT * FROM drivers WHERE driver_id = ?";
    if ($stmt = $db->prepare($query)) {
        $stmt->bind_param("i", $driver_id);
        $stmt->execute();
        $result = $stmt->get_result();

        // Fetch the driver data
        if ($row = $result->fetch_assoc()) {
            $driver = $row;
        } else {
            echo "Driver not found.";
            exit;
        }
    } else {
        echo "Error preparing statement.";
        exit;
    }
} else {
    echo "No driver selected.";
    exit;
}
?>

    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>EcoRide - Covoiturage</title>
        <link rel="icon" type="image/png" href="../public/assets/images/png/circle%20(1).png">
        <link rel="stylesheet" href="../public/assets/styles/reset.css">
        <link rel="stylesheet" href="../public/assets/styles/fonts.css">
        <link rel="stylesheet" href="../public/assets/styles/grid.css">
        <link rel="stylesheet" href="../public/assets/styles/app.css">
        <link rel="stylesheet" href="../public/assets/styles/responsive.css">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    </head>
    <body>
    <header class="header">
        <!--start menu-->
        <?php include_once "header.php" ?>
        <!--    finish menu-->
    </header>

    <div class="container">
        <div class="driver__wrapper">
            <h1 class="ecoride-prime__title" style="margin-bottom: 2rem">Interview Driver: <?= htmlspecialchars($driver['name']) ?></h1>

            <div class="driver-details">
                <!-- Driver Photo -->
                <img src="<?= htmlspecialchars($driver['photo']) ?>" alt="Driver Photo" class="driver-photo">

                <div class="driver-info">
                    <div class="driver-name"><?= htmlspecialchars($driver['name']) ?></div>
                    <div class="driver-rating-int">Rating: <?= htmlspecialchars($driver['rating']) ?></div>
                    <div class="driver-price">Price: $<?= htmlspecialchars($driver['price']) ?></div>
                </div>
            </div>

            <!-- Form to schedule an interview or send a message -->
            <h2 class="profits__first-title">Schedule an Interview or Ask a Question</h2>
            <form action="submitInterviewRequest.php" method="POST">
                <input type="hidden" name="driver_id" value="<?= htmlspecialchars($driver['driver_id']) ?>">

                <label for="interview_date">Preferred Interview Date:</label>
                <input type="date" name="interview_date" required><br><br>

                <label for="message">Message for the driver:</label><br>
                <textarea name="message" rows="4" cols="50" required style="padding: 1rem 1.5rem;color: var(--dark-green)"></textarea><br><br>

                <button type="submit" class="search-btn" style="margin-bottom: 3rem">Send Interview Request</button>
            </form>
        </div>
    </div>

    <footer class="footer">

        <!--    start footer-->
        <?php include_once "footer.php" ?>
        <!--    end footer-->

    </footer>
    </body>
    </html>
<?php
