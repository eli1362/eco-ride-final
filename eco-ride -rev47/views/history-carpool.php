<?php

global $db;
session_start(); // Start session
include_once "../config/Database.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = "Vous devez être connecté pour voir votre historique de covoiturage.";
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id']; // Logged-in user's ID

// Fetch driver_id from drivers table using user_id
$sql = "SELECT driver_id FROM drivers WHERE user_id = ?";
$stmt = $db->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$driver = $result->fetch_assoc();

if (!$driver) {
    echo "<p style='color:red; text-align:center;'>Erreur : Aucun profil conducteur trouvé pour cet utilisateur.</p>";
    exit;
}

$driver_id = $driver['driver_id']; // Get driver ID

// Fetch driver's carpool history
$sql = "SELECT * FROM carpool WHERE driver_id = ? ORDER BY departure_date DESC";
$stmt = $db->prepare($sql);
$stmt->bind_param("i", $driver_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoRide - Carpool History</title>
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
                    <a href="userPage1.php" class="menu__link">Dashboard</a>
                </li>

                <li class="menu__item">
                    <a href="history.php" class="menu__link">Historique des réservation</a>
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
                        <a href="userPage1.php" class="mobile-menu__link">Dashboard</a>
                    </li>

                    <li class="mobile-menu__item">
                        <a href="" class="mobile-menu__link">Credits</a>
                    </li>

                    <li class="mobile-menu__item">
                        <a href="history.php" class="mobile-menu__link">Historique des réservation</a>
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
<main style="background:linear-gradient(to bottom, #C8D5B9, #FAFAF0)" class="main">
    <div class="container">
        <section class="reservation-history">
            <h1 style="text-align: center; margin-bottom: 3rem">Votre Historique de Covoiturage</h1>
            <h2>Trajets à venir et passés</h2>
            <table border="1">
                <thead>
                <tr>
                    <th>ID Covoiturage</th>
                    <th>Date de Départ</th>
                    <th>Heure de Départ</th>
                    <th>Places Restantes</th>
                    <th>Prix</th>
                    <th>Annuler</th>
                </tr>
                </thead>
                <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['carpool_id']) ?></td>
                        <td><?= htmlspecialchars($row['departure_date']) ?></td>
                        <td><?= htmlspecialchars($row['departure_time']) ?></td>
                        <td><?= htmlspecialchars($row['remaining_seats']) ?></td>
                        <td><?= htmlspecialchars($row['price']) ?>€</td>
                        <td>
                            <form method="POST" action="cancel_carpool.php">
                                <input type="hidden" name="carpool_id" value="<?= htmlspecialchars($row['carpool_id']) ?>">
                                <button type="submit">Annuler</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
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

</body>
</html>


