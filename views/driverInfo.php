<?php
global $db;
session_start();
include_once '../config/Database.php';

// Query to get the top 10 drivers with rating above 3.5 and their reviews from the reservations table
$query = "
    SELECT d.driver_id, d.name, d.photo, d.rating AS driver_rating, d.price, r.rating AS review_rating, r.feedback
    FROM drivers d
    LEFT JOIN reservations r ON d.driver_id = r.driver_id
    WHERE d.rating > 3.5
    GROUP BY d.driver_id
    ORDER BY d.rating DESC
    LIMIT 10
";

$result = mysqli_query($db, $query);

// Check if there are drivers with a rating above 3.5
if ($result && mysqli_num_rows($result) > 0) {
    $drivers = mysqli_fetch_all($result, MYSQLI_ASSOC);
} else {
    $drivers = [];
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
    <main style="background:linear-gradient(to bottom, #C8D5B9, #FAFAF0)">
        <div class="container">
            <div class="driver__wrapper">
                <h1 class="ecoride-prime__title" style="margin-bottom: 2rem">Top 10 Drivers with Ratings Above 3.5</h1>
<div class="driver_info__wrapper">
                <?php if (count($drivers) > 0): ?>
                    <?php foreach ($drivers as $driver): ?>
                        <div class="driver-card">
                            <!-- Driver Photo -->
                            <img src="<?= htmlspecialchars($driver['photo']) ?>" alt="Driver Photo"
                                 class="driver-photo">

                            <div class="driver-info">
                                <!-- Driver Name -->
                                <div class="driver-name"><?= htmlspecialchars($driver['name']) ?></div>

                                <!-- Driver Rating -->
                                <div class="driver-rating">
                                    Rating: <?= htmlspecialchars($driver['driver_rating']) ?></div>

                                <!-- Driver Price -->
                                <div class="driver-price">Price: $<?= htmlspecialchars($driver['price']) ?></div>

                                <!-- Driver Feedback -->
                                <?php if ($driver['review_rating']): ?>
                                    <div class="driver-feedback">
                                        <strong>Review
                                            Rating: <?= htmlspecialchars($driver['review_rating']) ?></strong><br>
                                        <em>"<?= htmlspecialchars($driver['feedback']) ?>"</em>
                                    </div>
                                <?php else: ?>
                                    <div class="driver-feedback">
                                        <em>No reviews yet.</em>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Interview Button -->
                            <button onclick="window.location.href='interviewDriver.php?driver_id=<?= $driver['driver_id'] ?>'">
                                Interview
                            </button>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No drivers found with a rating above 3.5.</p>
                <?php endif; ?>
</div>
            </div>
        </div>
    </main>
    <footer class="footer">

        <!--    start footer-->
        <?php include_once "footer.php" ?>
        <!--    end footer-->

    </footer>

    </body>
    </html>
<?php
