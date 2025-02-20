
<?php
session_start();  // Start the session at the very beginning
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoRide - User Feedback</title>
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

<main style="background:linear-gradient(to bottom, #C8D5B9, #FAFAF0)" class="main">
    <div class="container">
        <section class="feedback-image-container">
            <div class="feedback-container">
                <h2 class="ecoride-prime__title feedback-title">Rate Your Trip</h2>

                <?php
                if (isset($_SESSION['success_message'])) {
                    echo '<div class="success-message">' . htmlspecialchars($_SESSION['success_message']) . '</div>';
                    unset($_SESSION['success_message']);  // Clear the message after displaying it
                }

                // Display error message if it's set
                if (isset($_SESSION['error_message'])) {
                    echo '<div class="error-message">' . htmlspecialchars($_SESSION['error_message']) . '</div>';
                    unset($_SESSION['error_message']);  // Clear the message after displaying it
                }
                ?>
                <form method="POST" action="submit_feedback.php" class="feedback-form">
                    <input type="hidden" name="reservation_id" value="<?= $_GET['reservation_id'] ?>">

                    <label for="rating">Rating (0-5):</label>
                    <input type="number" name="rating" min="0" max="5" class="feedback-label-number" required>

                    <label for="feedback">Feedback:</label>
                    <textarea name="feedback" required></textarea>

                    <button type="submit" class="search-btn search-btn-feedback">Submit</button>
                </form>
            </div>
            <div class="feedback-image">
                <img src="../public/assets/images/png/feedback.png" alt="feedback photo" class="feedback-photo">
            </div>
        </section>
    </div>
</main>

<footer class="footer">

    <!--    start footer-->
    <?php include_once "footer.php"?>
    <!--    end footer-->

</footer>
<script src="../public/assets/script/app.js"></script>
</body>
</html>
