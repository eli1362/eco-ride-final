<?php
// Enable error reporting for debugging
global $db;
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
include '../config/Database.php'; // Include your database connection file

// Check the connection
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Get form data
$depart = htmlspecialchars(trim($_POST['depart']));
$destination = htmlspecialchars(trim($_POST['destination']));
$date = htmlspecialchars(trim($_POST['date']));
$passenger = intval($_POST['passenger']);
$carType = htmlspecialchars(trim($_POST['carType']));
$time = htmlspecialchars(trim($_POST['time']));

// Validate input
if (empty($depart) || empty($destination) || empty($date) || empty($time) || empty($carType)) {
    echo "All fields are required.";
    exit();
}

// Prepare and execute the query to search for drivers matching the criteria
$stmt = $db->prepare("
    SELECT * FROM drivers 
    WHERE date = ? 
    AND remaining_seats >= ? 
    AND eco_friendly = ? 
    ORDER BY departure_time
");

$isEcoFriendly = ($carType === 'Electric') ? 1 : 0;
$stmt->bind_param("sii", $date, $passenger, $isEcoFriendly);
$stmt->execute();
$result = $stmt->get_result();
$drivers = $result->fetch_all(MYSQLI_ASSOC);

// If no exact matches, suggest nearby drivers
if (empty($drivers)) {
    $stmt = $db->prepare("
        SELECT * FROM drivers 
        WHERE remaining_seats >= ? 
        AND eco_friendly = ? 
        AND ABS(TIMESTAMPDIFF(DAY, date, ?)) <= 3
        ORDER BY ABS(TIMESTAMPDIFF(DAY, date, ?)), departure_time
    ");
    $stmt->bind_param("iiss", $passenger, $isEcoFriendly, $date, $date);
    $stmt->execute();
    $result = $stmt->get_result();
    $drivers = $result->fetch_all(MYSQLI_ASSOC);
}

// Display the drivers
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoRide - Drivers Result</title>
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
                <a href="index.html" class="app-logo">
                    <img src="../public/assets/images/png/logo.png" alt="logo image" class="app-logo__img">
                </a>
                <a href="index.html" class="logo-container__text "> ECO RIDE </a>
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
                    <a href="" class="menu__link">Trouver un trajet</a>
                </li>
                <li class="menu__item">
                    <a href="" class="menu__link">Proposer un trajet</a>
                </li>
                <li class="menu__item">
                    <a href="" class="menu__link">À propos de nous</a>
                </li>

            </ul>
            <div class="nav-menu">
                <ul class="mobile-menu">
                    <li class="mobile-menu__item">
                        <a href="" class="mobile-menu__link">Trouver un trajet</a>
                    </li>
                    <li class="mobile-menu__item">
                        <a href="" class="mobile-menu__link">Proposer un trajet</a>
                    </li>
                    <li class="mobile-menu__item">
                        <a href="" class="mobile-menu__link">Rechercher</a>
                    </li>
                    <li class="mobile-menu__item">
                        <a href="" class="mobile-menu__link">Nous contacter</a>
                    </li>
                    <li class="mobile-menu__item">
                        <a href="" class="mobile-menu__link">À propos de nous</a>
                    </li>

                </ul>
                <div id="mobile-menu" class="mobile-dropdown-menu">
                    <a href="loginPage.php" class="mobile-dropdown-item">Connection</a>
                    <a href="registerPage.php" class="mobile-dropdown-item mobile-dropdown-item--margin">Inscription</a>
                </div>

            </div>
            <div class="icon-dropdown">
                <div class="icon-dropdown-icon__container" id="toggleDropdown">
                    <i class="fa-solid fa-user icon-dropdown__icon"></i>
                    <i class="fa-solid fa-plus icon-dropdown__icon--plus" id="toggleIcon"></i>
                </div>
                <div id="menu" class="dropdown-menu">
                    <a href="loginPage.php" class="dropdown-item">Connection</a>
                    <a href="registerPage.php" class="dropdown-item">Inscription</a>
                </div>
            </div>
            <div class="nav__btn">
                <span class="nav__btn-line"></span>
            </div>
        </div>

    </div>

</header>
<main>
    <div class="container">
        <section class="search-results">
            <h1>Available Drivers</h1>
            <?php if (empty($drivers)) { ?>
                <p>No drivers are available near your selected date and time. Please try another search.</p>
            <?php } else { ?>
                <?php foreach ($drivers as $driver) { ?>
                    <div>
                        <p>Driver Name: <?php echo htmlspecialchars($driver['name']); ?></p>
                        <p>Departure: <?php echo $driver['date'] . ' at ' . $driver['departure_time']; ?></p>
                        <p>Arrival: <?php echo $driver['arrival_time']; ?></p>
                        <p>Seats Available: <?php echo $driver['remaining_seats']; ?></p>
                        <p>Eco-Friendly: <?php echo ($driver['eco_friendly'] ? "Yes" : "No"); ?></p>

                        <form action="reserveDriver.php" method="POST">
                            <input type="hidden" name="driver_id" value="<?php echo $driver['id']; ?>">
                            <input type="hidden" name="depart" value="<?php echo htmlspecialchars($depart); ?>">
                            <input type="hidden" name="destination" value="<?php echo htmlspecialchars($destination); ?>">
                            <input type="hidden" name="date" value="<?php echo htmlspecialchars($date); ?>">
                            <input type="hidden" name="time" value="<?php echo htmlspecialchars($time); ?>">
                            <input type="hidden" name="passenger" value="<?php echo $passenger; ?>">
                            <input type="hidden" name="car_type" value="<?php echo htmlspecialchars($carType); ?>">
                            <button type="submit">Reserve</button>
                        </form>
                    </div>
                <?php } ?>
            <?php } ?>
        </section>
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