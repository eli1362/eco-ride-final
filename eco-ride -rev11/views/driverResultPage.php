<?php
session_start();
// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Retrieve form data
    $depart = $_POST['depart'];
    $destination = $_POST['destination'];
    $date = $_POST['date']; // Format: DD/MM/YYYY
    $time = $_POST['time']; // Format: HH:MM

    // Convert the date (DD/MM/YYYY) into YYYY-MM-DD
    $dateParts = explode('/', $date); // Splitting the DD/MM/YYYY format
    if (count($dateParts) == 3) {
        // Convert to YYYY-MM-DD
        $date = $dateParts[2] . '-' . $dateParts[1] . '-' . $dateParts[0]; // Format: YYYY-MM-DD
    }

    // Combine the date and time into a datetime string for comparison or database entry
    $selectedDateTime = $date . ' ' . $time; // Format: YYYY-MM-DD HH:MM

    // Mock database of drivers
    $drivers = [
        // (Your driver data here...)
    ];

    // Find drivers who match the selected date and time
    $availableDriver = null;
    $alternativeDrivers = [];

    foreach ($drivers as $driver) {
        // Check if the driver is available at the selected time
        $driverAvailability = $driver['availability'];

        // Convert driver availability to datetime format for comparison
        foreach ($driverAvailability as $availability) {
            if ($selectedDateTime === $availability) {
                $availableDriver = $driver;
                break;
            } else if (abs(strtotime($selectedDateTime) - strtotime($availability)) <= 3600) {
                // If there's an alternative availability within an hour difference
                $alternativeDrivers[] = $driver;
            }
        }

        if ($availableDriver !== null) {
            break;
        }
    }
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
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
        }
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .driver-card {
            display: flex;
            align-items: center;
            background: #fff;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .driver-card img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin-right: 15px;
        }
        .driver-details {
            flex-grow: 1;
        }
        .driver-details h2 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }
        .driver-details p {
            margin: 5px 0;
            color: #666;
        }
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
                <a href="index.html" class="app-logo">
                    <img src="../public/assets/images/png/logo.png" alt="logo image" class="app-logo__img">
                </a>
                <a class="logo-container__text " href="index.html"> ECO RIDE </a>
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
                    <a href="" class="menu__link">Dashboard</a>
                </li>
                <li class="menu__item">
                    <a href="" class="menu__link">Reservation History</a>
                </li>
                <li class="menu__item">
                    <a href="" class="menu__link">Credits</a>
                </li>
                <li class="menu__item">
                    <a href="" class="menu__link">Logout</a>
                </li>

            </ul>
            <div class="nav-menu">
                <ul class="mobile-menu">
                    <li class="mobile-menu__item">
                        <a href="" class="mobile-menu__link">Dashboard</a>
                    </li>
                    <li class="mobile-menu__item">
                        <a href="" class="mobile-menu__link">Reservation History</a>
                    </li>
                    <li class="mobile-menu__item">
                        <a href="" class="mobile-menu__link">Credits</a>
                    </li>
                    <li class="mobile-menu__item">
                        <a href="" class="mobile-menu__link">Logout</a>
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
    <div class="driver-result">
        <?php if ($availableDriver !== null): ?>
            <!-- Show selected driver's information -->
            <div class="driver-info">
                <img src="<?php echo $availableDriver['photo']; ?>" alt="Driver Photo" class="driver-photo">
                <div class="driver-details">
                    <h2><?php echo $availableDriver['name']; ?></h2>
                    <p>Rating: <?php echo $availableDriver['rating']; ?></p>
                    <p>Price: $<?php echo $availableDriver['price']; ?></p>
                    <p>Departure Time: <?php echo $availableDriver['departure_time']; ?></p>
                    <p>Date: <?php echo $availableDriver['date']; ?></p>
                    <p>Eco-Friendly Ride: <?php echo ($availableDriver['eco_friendly'] ? "Yes" : "No"); ?></p>
                </div>
            </div>
        <?php else: ?>
            <p>No drivers available at the selected time.</p>
            <?php if (!empty($alternativeDrivers)): ?>
                <p>Alternative drivers available:</p>
                <?php foreach ($alternativeDrivers as $driver): ?>
                    <div class="driver-info">
                        <img src="<?php echo $driver['photo']; ?>" alt="Driver Photo" class="driver-photo">
                        <div class="driver-details">
                            <h2><?php echo $driver['name']; ?></h2>
                            <p>Rating: <?php echo $driver['rating']; ?></p>
                            <p>Price: $<?php echo $driver['price']; ?></p>
                            <p>Departure Time: <?php echo $driver['departure_time']; ?></p>
                            <p>Date: <?php echo $driver['date']; ?></p>
                            <p>Eco-Friendly Ride: <?php echo ($driver['eco_friendly'] ? "Yes" : "No"); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Sorry, no available alternatives found.</p>
            <?php endif; ?>
        <?php endif; ?>
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