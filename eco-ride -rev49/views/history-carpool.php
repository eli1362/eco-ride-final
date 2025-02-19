<?php
global $db;
session_start();
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
$sql = "
    SELECT 
        carpool.carpool_id,
        carpool.driver_id,
        carpool.departure_date,
        carpool.departure_time,
        carpool.remaining_seats,
        carpool.price,
        carpool.eco_friendly,
        carpool.user_id AS passenger_id,
        carpool.user_name AS passenger_name,
        users.email AS passenger_email
    FROM carpool
    JOIN users ON carpool.user_id = users.user_id
    WHERE carpool.driver_id = ? 
";

$stmt = $db->prepare($sql);
$stmt->bind_param("i", $driver_id);
$stmt->execute();
$result = $stmt->get_result();

// Store fetched rows in an array
$rows = [];
while ($row = $result->fetch_assoc()) {
    $row['credits'] = isset($row['eco_friendly']) ? ($row['eco_friendly'] ? 5 : 2) : 0;
    $rows[] = $row;
}
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
    <section class="reservation-history">
        <h1 class="ecoride-prime__title" style="text-align: center;margin-bottom: 3rem">
            Votre Historique de Covoiturage
        </h1>

        <?php if (empty($rows)): ?>
            <p class='no-history' style='text-align:center;'>Vous n'avez aucun historique de covoiturage.</p>
        <?php else: ?>

            <h2 style="color: var(--dark-green);margin-bottom: 1.5rem;text-align: center">
                Trajets à venir et passés
            </h2>

            <!-- Desktop Table -->
            <div class="history-table-container__desktop">
                <table class="history-table__history-page history-table__history-carpool">
                    <thead>
                    <tr>
                        <th>Nom du Passager</th>
                        <th>Email</th>
                        <th>Date de Départ</th>
                        <th>Heure de Départ</th>
                        <th>Places Restantes</th>
                        <th>Prix</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['passenger_name']) ?></td> <!-- Display Passenger's Name -->
                            <td><?= htmlspecialchars($row['passenger_email']) ?></td> <!-- Display Passenger's Email -->
                            <td><?= htmlspecialchars($row['departure_date']) ?></td>
                            <td><?= htmlspecialchars($row['departure_time']) ?></td>
                            <td><?= htmlspecialchars($row['remaining_seats']) ?></td>
                            <td><?= htmlspecialchars($row['price']) ?>€</td>
                            <td>
                                <?php if ($row['status'] == 'pending') { ?>
                                    <form method="POST" action="update_carpool_status.php">
                                        <input type="hidden" name="carpool_id" value="<?= $row['carpool_id'] ?>">
                                        <button type="submit" name="start_trip">Start Trip</button>
                                    </form>
                                <?php } elseif ($row['status'] == 'started') { ?>
                                    <form method="POST" action="update_carpool_status.php">
                                        <input type="hidden" name="carpool_id" value="<?= $row['carpool_id'] ?>">
                                        <button type="submit" name="finish_trip">Arrive at Destination</button>
                                    </form>
                                <?php } ?>
                            </td>
                            <td>
                                <form method="POST" action="cancel_carpool.php" onsubmit="return confirm('Êtes-vous sûr de vouloir annuler ce covoiturage?');">
                                    <input type="hidden" name="carpool_id" value="<?= htmlspecialchars($row['carpool_id']) ?>">
                                    <button type="submit" class="btn-cancel btn-cancel-history">Annuler</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <!-- Mobile Table -->
            <div class="history-table-container__mobile">
                <table class="history-table__history-page__mobile">
                    <tbody>
                    <?php
                    $labels = [
                        "ID Covoiturage" => "carpool_id",
                        "Nom du Passager" => "passenger_name", // Added Passenger Name
                        "Email" => "passenger_email", // Added Passenger Email
                        "Nom Conducteur" => "driver_name",
                        "Date de Départ" => "departure_date",
                        "Heure de Départ" => "departure_time",
                        "Places Restantes" => "remaining_seats",
                        "Prix" => "price",
                    ];
                    foreach ($labels as $title => $key): ?>
                        <tr>
                            <th><?= $title ?></th>
                            <?php foreach ($rows as $row): ?>
                                <td>
                                    <?= htmlspecialchars($row[$key] ?? 'N/A') ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                    <!-- Status Button Row -->
                    <tr>
                        <th>Statut</th>
                        <?php foreach ($rows as $row): ?>
                            <td>
                                <?php if ($row['status'] == 'pending') { ?>
                                    <form method="POST" action="update_carpool_status.php">
                                        <input type="hidden" name="carpool_id" value="<?= $row['carpool_id'] ?>">
                                        <button type="submit" name="start_trip" class="btn-status">Démarrer</button>
                                    </form>
                                <?php } elseif ($row['status'] == 'started') { ?>
                                    <form method="POST" action="update_carpool_status.php">
                                        <input type="hidden" name="carpool_id" value="<?= $row['carpool_id'] ?>">
                                        <button type="submit" name="finish_trip" class="btn-status">Arrivé à destination</button>
                                    </form>
                                <?php } else { ?>
                                    Terminé
                                <?php } ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>

                    <!-- Cancel Button Row -->

                    <!-- Cancel Button Row -->
                    <tr>
                        <th>Action</th>
                        <?php foreach ($rows as $row): ?>
                            <td>
                                <form method="POST" action="cancel_carpool.php" onsubmit="return confirm('Êtes-vous sûr de vouloir annuler ce covoiturage?');">
                                    <input type="hidden" name="carpool_id" value="<?= htmlspecialchars($row['carpool_id']) ?>">
                                    <button type="submit" class="btn-cancel btn-cancel-history">Annuler</button>
                                </form>
                            </td>
                        <?php endforeach; ?>
                    </tr>

                    </tbody>
                </table>
            </div>

        <?php endif; ?>
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


