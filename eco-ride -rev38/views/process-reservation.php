<?php

global $db;
session_start();
include_once '../config/Database.php'; // Adjust path as needed

// Initialize variables
$depart = $destination = $date = $departure_time = $passenger = $carType = "";
$availableDrivers = [];

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $depart = $_POST['depart'] ?? "";
    $destination = $_POST['destination'] ?? "";
    $date = $_POST['date'] ?? "";
    $departure_time = $_POST['departure_time'] ?? "";
    $passenger = isset($_POST['passenger']) ? (int)$_POST['passenger'] : 0;
    $carType = $_POST['carType'] ?? "";

    // Check if the user is logged in
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['error_message'] = "Vous devez être connecté pour réserver un trajet.";
        header("Location: index.php");
        exit();
    }
    $user_id = $_SESSION['user_id'];

    // Validate required inputs
    if (empty($date) || empty($departure_time) || $passenger <= 0) {
        $_SESSION['error_message'] = "Please provide all required fields (date, time, passenger count)";
        header("Location: index.php");
        exit();
    }

    // Convert date and time into datetime format (use full time format HH:MM:SS)
    $departure_time = date('H:i:s', strtotime($departure_time)); // Convert to full time format
    $dateTime = date('Y-m-d H:i:s', strtotime("$date $departure_time"));


    $sqlExact = "
    SELECT * FROM drivers
    WHERE date = ? 
    AND departure_time = ? 
    AND remaining_seats >= ?
";
    $stmtExact = $db->prepare($sqlExact);

// Assuming $date and $time are strings (in 'YYYY-MM-DD' and 'HH:MM:SS' format)
    $stmtExact->bind_param("ssi", $date, $departure_time, $passenger);  // 'sss' since date and time are strings, remaining_seats is integer

    $stmtExact->execute();
    $resultExact = $stmtExact->get_result();

    while ($row = $resultExact->fetch_assoc()) {
        $availableDrivers[] = $row;
    }


    // If no exact match, try searching for drivers within ±3 days
    if (empty($availableDrivers)) {
        // Adjust date range (±3 days)
        $dateWindowStart = date('Y-m-d', strtotime("-3 days", strtotime($date)));
        $dateWindowEnd = date('Y-m-d', strtotime("+3 days", strtotime($date)));

        // Query within ±3 days and remaining seats >= passengers
        $sqlAlternative = "
        SELECT * FROM drivers
        WHERE remaining_seats >= ? 
        AND date BETWEEN ? AND ?
        ORDER BY ABS(TIMESTAMPDIFF(DAY, date, ?)) ASC
        LIMIT 5
        ";
        $stmtAlternative = $db->prepare($sqlAlternative);
        $stmtAlternative->bind_param("isss", $passenger, $dateWindowStart, $dateWindowEnd, $date);
        $stmtAlternative->execute();
        $resultAlternative = $stmtAlternative->get_result();

        while ($row = $resultAlternative->fetch_assoc()) {
            $availableDrivers[] = $row;
        }

        $stmtAlternative->close();
    }

    $stmtExact->close();
    $db->close();
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoRide - User dashboard</title>
    <link rel="icon" type="image/png" href="../public/assets/images/png/circle%20(1).png">
    <link rel="stylesheet" href="../public/assets/styles/reset.css">
    <link rel="stylesheet" href="../public/assets/styles/fonts.css">
    <link rel="stylesheet" href="../public/assets/styles/grid.css">
    <link rel="stylesheet" href="../public/assets/styles/app.css">
    <link rel="stylesheet" href="../public/assets/styles/responsive.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>





        .no-drivers {
            text-align: center;
            padding: 20px;
            background: #ffefef;
            color: #c00;
            border: 1px solid #f5c6cb;
            border-radius: 8px;
        }
    </style>

</head>

<body>
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
<main class="main">
    <div class="container">
    <div class="driver-result__wrapper">
        <h1 class="ecoride-prime__title driver__title">Available Drivers</h1>
        <div class="driver__wrapper">
            <?php if (!empty($availableDrivers)): ?>
                <?php foreach ($availableDrivers as $driver): ?>
                    <div class="driver-card">
                        <img src="<?php echo htmlspecialchars($driver['photo']); ?>" alt="Driver Photo">
                        <div class="driver-details">
                            <h2><?php echo htmlspecialchars($driver['name']); ?></h2>
                            <div class="driver-info driver-rating">
                                <?php
                                $rating = $driver['rating'];
                                for ($i = 1; $i <= 5; $i++): ?>
                                    <?php if ($i <= $rating): ?>
                                        <span class="star full-star">★</span>
                                    <?php else: ?>
                                        <span class="star empty-star">☆</span>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            </div>

                            <p class="driver-info">Price: <span><?php echo htmlspecialchars($driver['price']); ?> $</span></p>
                            <p class="driver-info">Departure Time: <span><?php echo htmlspecialchars($driver['departure_time']); ?></span></p>
                            <p class="driver-info">Date: <span><?php echo htmlspecialchars($driver['date']); ?></span></p>
                            <p class="driver-info">Eco-Friendly: <span><?php echo $driver['eco_friendly'] ? "Yes" : "No"; ?></span></p>
                        </div>

                        <form action="reservation.php" method="POST">
                            <input type="hidden" name="driver_id" value="<?php echo $driver['driver_id']; ?>">
                            <input type="hidden" name="driver_name" value="<?php echo htmlspecialchars($driver['name']); ?>">
                            <input type="hidden" name="driver_price" value="<?php echo htmlspecialchars($driver['price']); ?>">
                            <input type="hidden" name="departure_time" value="<?php echo htmlspecialchars($driver['departure_time']); ?>">
                            <input type="hidden" name="date" value="<?php echo htmlspecialchars($driver['date']); ?>">
                            <input type="hidden" name="eco_friendly" value="<?php echo $driver['eco_friendly'] ? 1 : 0; ?>">
                            <input type="hidden" name="passenger" value="<?php echo $passenger; ?>">
                            <button type="submit" class="reserve-button">Reserve</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-drivers">
                    <p>No drivers available for the selected criteria.</p>
                </div>
            <?php endif; ?>
        </div>
        <div class="animated-message">
            <h4 class="animate-text"><span class="bold">En réservant une voiture électrique,</span> vous contribuez à un <span class="bold">avenir plus propre et plus sain</span> pour notre planète, tout en accumulant davantage de <span class="bold">crédits</span> pour vos futures réservations !</h4>
            <img alt="car photo" src="../public/assets/images/png/car-reservation.png" class="car-image" ">
        </div>
     </div>
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
</body>
</html>
