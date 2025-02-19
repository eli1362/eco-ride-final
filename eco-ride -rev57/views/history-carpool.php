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


// Fetch driver's carpool history including the status
$sql = "
    SELECT 
        carpool.carpool_id,
        carpool.driver_id,
        carpool.departure_date,
        carpool.departure_time,
        carpool.remaining_seats,
        carpool.price,
        carpool.eco_friendly,
        carpool.status,  
        carpool.user_id AS passenger_id,
        carpool.user_name AS passenger_name,
        users.email AS passenger_email
    FROM carpool
    JOIN users ON carpool.user_id = users.user_id
    WHERE carpool.driver_id = ? AND status != 'finished'
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
// Check if the user clicks on 'Start' or 'Finish' buttons
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['start_trip'])) {
        // Change the carpool status to 'started'
        $carpool_id = $_POST['carpool_id'];
        $update_sql = "UPDATE carpool SET status = 'started' WHERE carpool_id = ?";
        $update_stmt = $db->prepare($update_sql);
        $update_stmt->bind_param("i", $carpool_id);
        $update_stmt->execute();
        $update_stmt->close();

        $_SESSION['success_message'] = "Le covoiturage a démarré avec succès.";
    }

    if (isset($_POST['finish_trip'])) {
        // Change the carpool status to 'finished'
        $carpool_id = $_POST['carpool_id'];
        $update_sql = "UPDATE carpool SET status = 'finished' WHERE carpool_id = ?";
        $update_stmt = $db->prepare($update_sql);
        $update_stmt->bind_param("i", $carpool_id);
        $update_stmt->execute();
        $update_stmt->close();

        // Optionally, here you can handle the review system
        $_SESSION['success_message'] = "Le covoiturage est terminé. Merci pour votre service.";
    }


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
<main style="background: linear-gradient(to bottom, #C8D5B9, #FAFAF0)" class="main">
    <section class="reservation-history">

        <h1 class="ecoride-prime__title" style="text-align: center; margin-bottom: 3rem">
            Votre Historique de Covoiturage
        </h1>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="error-message"><?= $_SESSION['error_message'] ?></div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <?php if (empty($rows)): ?>
            <p class="no-history" style="text-align: center;">Vous n'avez aucun historique de covoiturage.</p>
        <?php else: ?>

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
                            <td><?= htmlspecialchars($row['passenger_name']) ?></td>
                            <td><?= htmlspecialchars($row['passenger_email']) ?></td>
                            <td><?= htmlspecialchars($row['departure_date']) ?></td>
                            <td><?= htmlspecialchars($row['departure_time']) ?></td>
                            <td><?= htmlspecialchars($row['remaining_seats']) ?></td>
                            <td><?= htmlspecialchars($row['price']) ?>€</td>

                            <td class="status" id="desktop-status-<?= $row['carpool_id'] ?>" data-carpool-id="<?= $row['carpool_id'] ?>">
                                <?php if ($row['status'] == 'pending') { ?>
                                    <!-- Form to start the trip -->
                                    <form method="POST" action="update_carpool_status.php">
                                        <input type="hidden" name="carpool_id" value="<?= $row['carpool_id'] ?>">
                                        <button type="submit" name="start_trip" class="btn-start-trip btn-cancel-history btn-cancel">Démarrer le trajet</button>
                                    </form>
                                <?php } elseif ($row['status'] == 'started') { ?>
                                    <!-- Form to mark as arrived at destination -->
                                    <form method="POST" action="update_carpool_status.php">
                                        <input type="hidden" name="carpool_id" value="<?= $row['carpool_id'] ?>">
                                        <button type="submit" name="finish_trip" class="btn-finish-trip btn-cancel-history btn-cancel">Arrivé à destination</button>
                                    </form>
                                <?php } else { ?>
                                    <!-- Status is finished -->
                                    <span style="color: grey;">Terminé</span>
                                <?php } ?>
                            </td>

                            <td id="action-<?= $row['carpool_id'] ?>">
                                <?php if ($row['status'] !== 'finished'): ?>
                                    <form method="POST" action="cancel_carpool.php" class="cancel-form"
                                          onsubmit="return confirm('Êtes-vous sûr de vouloir annuler ce covoiturage?');">
                                        <input type="hidden" name="carpool_id" value="<?= htmlspecialchars($row['carpool_id']) ?>">
                                        <button type="submit" class="btn-cancel btn-cancel-history btn-cancel">Annuler</button>
                                    </form>
                                <?php else: ?>
                                    <span style="color: grey;">C'est déjà fait</span>
                                <?php endif; ?>
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
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <th>Nom du Passager</th>
                            <td><?= htmlspecialchars($row['passenger_name']) ?></td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td><?= htmlspecialchars($row['passenger_email']) ?></td>
                        </tr>
                        <tr>
                            <th>Date de Départ</th>
                            <td><?= htmlspecialchars($row['departure_date']) ?></td>
                        </tr>
                        <tr>
                            <th>Heure de Départ</th>
                            <td><?= htmlspecialchars($row['departure_time']) ?></td>
                        </tr>
                        <tr>
                            <th>Places Restantes</th>
                            <td><?= htmlspecialchars($row['remaining_seats']) ?></td>
                        </tr>
                        <tr>
                            <th>Prix</th>
                            <td><?= htmlspecialchars($row['price']) ?>€</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td class="status" id="mobile-status-<?= $row['carpool_id'] ?>" data-carpool-id="<?= $row['carpool_id'] ?>">
                                <?php if ($row['status'] === 'pending'): ?>
                                    <!-- Form for "Start Trip" on mobile -->
                                    <form method="POST" action="update_carpool_status.php">
                                        <input type="hidden" name="carpool_id" value="<?= $row['carpool_id'] ?>">
                                        <button type="submit" name="start_trip" class="btn-start-trip btn-cancel-history btn-cancel">Démarrer le trajet</button>
                                    </form>
                                <?php elseif ($row['status'] === 'started'): ?>
                                    <!-- Form for "Arrive at Destination" on mobile -->
                                    <form method="POST" action="update_carpool_status.php">
                                        <input type="hidden" name="carpool_id" value="<?= $row['carpool_id'] ?>">
                                        <button type="submit" name="finish_trip" class="btn-finish-trip btn-cancel-history btn-cancel">Arrivé à destination</button>
                                    </form>
                                <?php else: ?>
                                    <!-- Status is finished -->
                                    <span style="color: grey;">Terminé</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Action</th>

                            <td id="action-<?= $row['carpool_id'] ?>" class="mobile-action">
                                <?php if ($row['status'] !== 'finished'): ?>
                                    <form method="POST" action="cancel_carpool.php" class="cancel-form"
                                          onsubmit="return confirm('Êtes-vous sûr de vouloir annuler ce covoiturage?');">
                                        <input type="hidden" name="carpool_id" value="<?= htmlspecialchars($row['carpool_id']) ?>">
                                        <button type="submit" class="btn-cancel btn-cancel-history btn-cancel" id="cancel-btn-<?= $row['carpool_id'] ?>">Annuler</button>
                                    </form>
                                <?php else: ?>
                                    <span style="color: grey;">C'est déjà fait</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
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
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Select all the "Démarrer le trajet" buttons and handle clicks
        document.querySelectorAll('.btn-start-trip').forEach(button => {
            button.addEventListener('click', function () {
                const carpoolId = this.getAttribute('data-carpool-id');

                // Handle Desktop: Change "Démarrer le trajet" to "Arrivé à destination"
                const desktopStatusCell = document.querySelector(`#desktop-status-${carpoolId}`);
                if (desktopStatusCell) {
                    this.style.display = 'none';  // Hide the "Démarrer le trajet" button
                    const arrivedText = document.createElement('p');
                    arrivedText.textContent = 'Arrivé à destination';
                    arrivedText.style.color = 'var(--dark-green)';
                    arrivedText.style.fontWeight = '550';
                    desktopStatusCell.appendChild(arrivedText);
                }

                // Handle Mobile: Same logic for mobile to change text when "Démarrer le trajet" is clicked
                const mobileStatusCell = document.querySelector(`#mobile-status-${carpoolId}`);
                if (mobileStatusCell) {
                    this.style.display = 'none';  // Hide the "Démarrer le trajet" button
                    const arrivedText = document.createElement('p');
                    arrivedText.textContent = 'Arrivé à destination';
                    arrivedText.style.color = 'var(--dark-green)';
                    arrivedText.style.fontWeight = '550';
                    mobileStatusCell.appendChild(arrivedText);
                }

                // Change the "Annuler" button to "C'est déjà fait" for both desktop and mobile
                const actionCell = document.querySelector(`#action-${carpoolId}`);
                if (actionCell) {
                    const cancelButton = actionCell.querySelector('.btn-cancel');
                    if (cancelButton) {
                        cancelButton.parentElement.innerHTML = "<span style='color: grey;'>C'est déjà fait</span>";
                    }
                }

                // Change the "Annuler" button to "C'est déjà fait" for mobile view
                const cancelButton = document.querySelector(`#cancel-btn-${carpoolId}`);
                if (cancelButton) {
                    cancelButton.parentElement.innerHTML = "<span style='color: grey;'>C'est déjà fait</span>";
                }


            });
        });
    });
</script>


<script src="../public/assets/script/app.js"></script>



</body>
</html>


