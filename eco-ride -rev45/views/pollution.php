<?php
global $db;
session_start();
include_once '../config/Database.php';

if (!isset($_GET['reservation_id'])) {
    header("Location: userPage1.php"); // Redirect to home if no reservation ID
    exit();
}

$driver_name = $_GET['driver_name'] ?? "Unknown Driver";
$passenger = $_GET['passenger'] ?? 1;
$date = $_GET['date'] ?? "Unknown Date";
$departure_time = $_GET['departure_time'] ?? "Unknown Time";
$reservation_id = $_GET['reservation_id'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoRide - Reservetion page</title>
    <link rel="icon" type="image/png" href="../public/assets/images/png/circle%20(1).png">
    <link rel="stylesheet" href="../public/assets/styles/reset.css">
    <link rel="stylesheet" href="../public/assets/styles/fonts.css">
    <link rel="stylesheet" href="../public/assets/styles/grid.css">
    <link rel="stylesheet" href="../public/assets/styles/app.css">
    <link rel="stylesheet" href="../public/assets/styles/responsive.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>

        .cancel-btn {
            display: inline-block;
            background: red;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            margin-top: 20px;
        }

    </style>

</head>
<body style="background: linear-gradient(to right, #3A3A3A, #18704de0);"


" >
<header class="header">
    <!--    first navbar -->

    <section class="desktopNav-address">
        <div class="desktopNav-address__group desktopNav-address__group-paddingRight">

            <i class="fa-regular fa-clock desktopNav-address__logo"></i>
            <p class="desktopNav-address__text">
                Du lundi au samdi,de 18:00 a minuit
            </p>

        </div>
        <div class="desktopNav-address__group">

            <i class="fa-solid fa-phone desktopNav-address__logo"></i>
            <p class="desktopNav-address__text">
                +1 234 567 890
            </p>

        </div>
        <div class="desktopNav-address__group desktopNav-address__group-paddingRight">

            <i class="fa-solid fa-location-dot desktopNav-address__logo"></i>
            <p class="desktopNav-address__text">
                123 Rue Principale,paris
            </p>

        </div>

    </section>

    <!--    second navbar menu -->

    <div class="nav">
        <div class="logo-search__wrapper">
            <div class="logo-container">
                <a href="index.php" class="app-logo">
                    <img src="../public/assets/images/png/logo.png" alt="logo image" class="app-logo__img">
                </a>
                <a class="logo-container__text " href="index.php"> ECO RIDE </a>
            </div>
            <div class="search">
                <label>
                    <input type="text" class="search__input" placeholder="Recherche">
                </label>

                <i class="fa-solid fa-magnifying-glass search__icon"></i>

            </div>
        </div>


        <div class="menu-icon__wrapper">
            <ul class="menu">
                <li class="menu__item">
                    <a href="userPage1.php" class="menu__link">Dashboard</a>
                </li>
                <li class="menu__item">
                    <a href="history.php" class="menu__link">Reservation History</a>
                </li>

                <li class="menu__item">
                    <a href="logoutPage.php" class="menu__link">Logout</a>
                </li>

            </ul>
            <div class="nav-menu">
                <ul class="mobile-menu">
                    <li class="mobile-menu__item">
                        <a href="userPage1.php" class="mobile-menu__link">Dashboard</a>
                    </li>
                    <li class="mobile-menu__item">
                        <a href="history.php" class="mobile-menu__link">Reservation History</a>
                    </li>

                    <li class="mobile-menu__item">
                        <a href="logoutPage.php" class="mobile-menu__link">Logout</a>
                    </li>

                </ul>

            </div>

            <div class="nav__btn">
                <span class="nav__btn-line"></span>
            </div>
        </div>

    </div>

</header>

<main >
    <div class="container">
    <div class="reservation-photo__wrapper">
    <section class="reservation__wrapper">
    <h1 class="ecoride-prime__title" style="color:#ffffff;">Process Your Reservation</h1>

    <?php if (isset($_SESSION['success_message'])) : ?>
        <h3 class="success profits__first-title" style="color:#ffffff;">
            <?= htmlspecialchars($_SESSION['success_message']) ?>
            <?php unset($_SESSION['success_message']); ?>
        </h3>
    <?php endif; ?>


        <h2 class=" profits__first-title" style="color:#f44336;font-size: 2rem;">By choosing a non-electric car, you are contributing to pollution.</h2>
        <h3 class=" profits__first-title" style="color:#f44336;">Consider switching to <strong style="color:#4CAF50;font-size:2.1rem">an electric vehicle</strong>  for a cleaner environment.</h3>

        <h3 style="font-family: 'Montserrat-semiBold',serif;margin-top: 2rem;color: #f44336" class="driver-info">
            You reserved the driver : <strong style="color:#fff"><?= htmlspecialchars($driver_name); ?></strong><br><br>
            With : <strong style="color:#fff "><?= htmlspecialchars($passenger); ?> passengers</strong> <br><br>
            On : <strong style="color:#fff"><?= htmlspecialchars($date); ?></strong><br><br>
            At : <strong style="color:#fff"><?= htmlspecialchars($departure_time); ?></strong><br><br>
            <h4 style="color:#fff;font-family: 'Montserrat-semiBold', serif;margin-bottom: 2rem">
                If for any reason you don't want it, you can cancel your travel by clicking this button.
            </h4>
            <a href="cancelReservation.php?id=<?= htmlspecialchars($reservation_id); ?>" class="btn-cancel">Cancel</a>
        </h3>

    </section>
    <div class="ecoride-prime__photo">
        <img alt="pollution car" src="../public/assets/images/png/pollution.png" class="ecoride-prime__img">
    </div>
    </div>

<section class="alternative-driver">
    <!-- Available Electric Drivers Section -->


    <?php
    // Query for electric cars within the 3-day window (before or after the selected date)
    $sqlElectric = "
    SELECT * 
    FROM drivers
    WHERE eco_friendly = 1 
    AND remaining_seats >= ? 
    AND ABS(TIMESTAMPDIFF(DAY, CONCAT(date, ' ', departure_time), CONCAT(?, ' ', ?))) <= 3
    ORDER BY ABS(TIMESTAMPDIFF(MINUTE, CONCAT(date, ' ', departure_time), CONCAT(?, ' ', ?))) ASC
    ";

    $stmtElectric = $db->prepare($sqlElectric);
    $stmtElectric->bind_param("issss", $passenger, $date, $departure_time, $date, $departure_time);
    $stmtElectric->execute();
    $resultElectric = $stmtElectric->get_result();

    if ($resultElectric->num_rows > 0) {
        while ($row = $resultElectric->fetch_assoc()) {
            // Display electric drivers in the card format
            echo '<div class="driver-card">';
            echo '<img src="' . htmlspecialchars($row['photo']) . '" alt="Driver Photo">';
            echo '<div class="driver-details">';
            echo '<h2>' . htmlspecialchars($row['name']) . '</h2>';
            echo '<div class="driver-info driver-rating">';

            // Display rating stars
            $rating = $row['rating'];
            for ($i = 1; $i <= 5; $i++) :
                if ($i <= $rating): ?>
                    <span class="star full-star">★</span>
                <?php else: ?>
                    <span class="star empty-star">☆</span>
                <?php endif;
            endfor;

            echo '</div>';
            echo '<p class="driver-info">Price: <span>' . htmlspecialchars($row['price']) . ' $</span></p>';
            echo '<p class="driver-info">Departure Time: <span>' . htmlspecialchars($row['departure_time']) . '</span></p>';
            echo '<p class="driver-info">Date: <span>' . htmlspecialchars($row['date']) . '</span></p>';
            echo '<p class="driver-info">Eco-Friendly: <span>Yes</span></p>';
            echo '</div>';

            // Add button to reserve the electric driver, with logic to cancel the previous non-electric reservation
            echo '<a href="reserveElectricDriver.php?driver_id=' . htmlspecialchars($row['driver_id']) . '" class="reserve-button">Reserve This Driver</a>';

            echo '</div>';
        }
    } else {
        echo '<p>No electric drivers available at this time.</p>';
    }
    $stmtElectric->close();
    ?>



</section>
    </div>
</main>
<footer class="footer">
    <div class="container">

        <div class="footer-container">
            <!-- Left Section: Menu Links -->
            <div class="footer-menu">
                <a href="#">Accueil</a>
                <a href="#">Trouver un trajet</a>
                <a href="#">Proposer un trajet</a>
                <a href="#">À propos de nous</a>
                <a href="#">Nous contacter</a>
                <a href="#">Se connecter / S'inscrire</a>
            </div>

            <!-- Center Section: Logo -->
            <div class="footer-logo">
                <img src="../public/assets/images/png/logo.png" alt="Eco Ride Logo">
                <span class="logo-container__text ">Eco Ride</span>
            </div>

            <!-- Right Section: Search Bar and Social Media Icons -->
            <div class="footer-right">
                <div class="search search__footer">
                    <label>
                        <input type="text" class="search__input" placeholder="Recherche">
                    </label>

                    <i class="fa-solid fa-magnifying-glass search__icon"></i>

                </div>
                <div class="social-icons">
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>

        <!-- Bottom Section: Copyright -->
        <div class="footer-bottom">
            © 2024 Your Company. All Rights Reserved
        </div>
    </div>
</footer>

<script src="../public/assets/script/app.js"></script>
</html>
