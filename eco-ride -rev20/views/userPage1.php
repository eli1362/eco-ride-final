<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    // If not logged in, redirect to login page
    header("Location: loginPage.php");
    exit();
}

// If logged in, you can use the user data
$user = $_SESSION['user']; // Access logged-in user data
$user_id = $user['user_id']; // Access user_id if needed
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
                    <a href="" class="menu__link">Dashboard</a>
                </li>
                <li class="menu__item">
                    <a href="" class="menu__link">Reservation History</a>
                </li>
                <li class="menu__item">
                    <a href="" class="menu__link">Credits</a>
                </li>
                <li class="menu__item">
                    <a href="logoutPage.php" class="menu__link">Logout</a>
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
                        <a href="logoutPage.php" class="mobile-menu__link">Logout</a>
                    </li>

                </ul>

            </div>

            <div class="nav__btn">
                <span class="nav__btn-line"></span>
            </div>
        </div>

    </div>

    <!--    car renting menu -->

    <section class="car-renting__wrapper user-wrapper">
        <div class="container">
            <!-- Welcome Message -->
            <section class="welcome">
                <h1 class="car-renting__text user-text ">
                    <?php
                    echo "Content de vous revoir, " . htmlspecialchars($user['full_name']) . "!"; ?>
                </h1>
                <p class="welcome__text">Que souhaiteriez-vous faire aujourd'hui ?</p>

</header>
<main class="main">
    <div class="container">
       <!-- Dashboard Actions -->
        <section class="dashboard-actions profits-wrappers dashboards-wrapper">
            <div class="action-card profits-wrapper dashboard-wrapper">
                <i class="fa-solid fa-car action-card__icon"></i>
                <h3 class="profits__first-title">Réserver une voiture</h3>
                <a href="index.php" class="action-card__btn search-btn dashboard-btn">Réservez maintenant</a>
            </div>
            <div class="action-card profits-wrapper dashboard-wrapper">
                <i class="fa-solid fa-clock-rotate-left action-card__icon"></i>
                <h3 class="profits__first-title">Afficher l'historique des réservations</h3>
                <a href="history.php" class="action-card__btn search-btn dashboard-btn">Voir l'historique</a>
            </div>
            <div class="action-card profits-wrapper dashboard-wrapper">
                <i class="fa-solid fa-wallet action-card__icon"></i>
                <h3 class="profits__first-title">Vérifier les crédits</h3>
                <a href="#" class="action-card__btn search-btn dashboard-btn">Voir le solde</a>
            </div>
        </section>
        <!-- History Section -->
        <section class="history">
            <h2 class="user__title">Réservations récentes</h2>
            <table class="history-table">

                <tbody id="reservationHistory">
                <tr>
                    <td data-label="Date">2025-01-01</td>
                    <td data-label="Modèle de voiture">Toyota Prius</td>
                    <td data-label="Lieu de ramassage">Paris Center</td>
                    <td data-label="Lieu de dépôt">Airport</td>
                    <td data-label="Prix">€50</td>
                    <td data-label="Vos crédits">€100 Available</td>
                </tr>
                <!-- Additional rows will be dynamically added -->
                </tbody>
            </table>
        </section>
        <!--Next ride -->
        <section class="illustration-section">
            <h2 class="illustration-title">Planifiez votre prochaine sortie</h2>
            <div class="illustration-container">
                <!-- SVG for Irregular Oval Background -->
                <svg class="svg-background" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 300" preserveAspectRatio="none">
                    <ellipse cx="200" cy="150" rx="200" ry="100" fill="rgba(0, 128, 0, 0.1)" />
                </svg>

                <!-- Car Illustration -->
                <img src="../public/assets/images/png/City%20driver-pana.png" alt="Illustration of a car and map" class="illustration-image">
            </div>
            <p class="illustration-text">Trouvez et réservez rapidement vos trajets grâce à notre plateforme de réservation transparente.</p>
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

