<?php
global $db;
session_start();
include_once '../config/Database.php'; // Adjust the path to your database configuration file

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = "Vous devez être connecté pour réserver un trajet.";
    header("Location: index.php");
}

$user_id = $_SESSION['user_id'];

// Fetch the reservation history for the logged-in user
$sqlFetchHistory = "
    SELECT r.reservation_id, r.driver_id, r.driver_name, r.driver_price, r.departure_time, 
           r.date, r.eco_friendly, r.passenger, r.reservation_date, r.status 
    FROM reservations r
    WHERE r.user_id = ?
    ORDER BY r.reservation_date DESC";

$stmtFetchHistory = $db->prepare($sqlFetchHistory);
$stmtFetchHistory->bind_param("i", $user_id);
$stmtFetchHistory->execute();
$resultHistory = $stmtFetchHistory->get_result();
$rows = $resultHistory->fetch_all(MYSQLI_ASSOC);

// Close the statement and database connection
$stmtFetchHistory->close();
$db->close();
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
                    <a href="userPage1.php" class="menu__link">Dashboard</a>
                </li>

                <li class="menu__item">
                    <a href="history-carpool.php" class="menu__link">Historique des carpooling</a>
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
                        <a href="history-carpool.php" class="mobile-menu__link">Historique des carpooling</a>
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
            <h1 class="ecoride-prime__title" style="text-align: center;margin-bottom: 3rem">Your Reservation
                History</h1>
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

            <?php if ($resultHistory->num_rows === 0): ?>
                <p class="no-history">You have no reservation history.</p>
            <?php else: ?>
                <table class="history-table__history-page">
                    <thead>
                    <tr>
                        <th>Driver Name</th>
                        <th>Price</th>
                        <th>Departure Time</th>
                        <th>Date</th>
                        <th>Eco-Friendly</th>
                        <th>Passengers</th>
                        <th>Credits Earned</th>
                        <th>Reservation Date</th>
                        <th>Feedback</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['driver_name'] ?? 'N/A') ?></td>
                            <td>$<?= htmlspecialchars($row['driver_price'] ?? '0') ?></td>
                            <td><?= htmlspecialchars($row['departure_time'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($row['date'] ?? 'N/A') ?></td>
                            <td><?= isset($row['eco_friendly']) ? ($row['eco_friendly'] ? "Yes" : "No") : "N/A" ?></td>
                            <td><?= htmlspecialchars($row['passenger'] ?? '0') ?></td>
                            <td><?= isset($row['eco_friendly']) ? ($row['eco_friendly'] ? 5 : 2) : 0 ?></td>
                            <td><?= htmlspecialchars($row['reservation_date'] ?? 'N/A') ?></td>

                            <td>
                                <?php if (empty($row['feedback'])): ?>
                                    <!-- Button to go to feedback form if feedback is empty -->
                                    <form method="GET" action="feedback_form.php">
                                        <input type="hidden" name="reservation_id" value="<?= $row['reservation_id'] ?>">
                                        <button type="submit" class="btn-feedback btn-cancel btn-cancel-history">Laisser un avis</button>
                                    </form>
                                <?php else: ?>
                                    <!-- Display the feedback if already provided -->
                                    <?= nl2br(htmlspecialchars($row['feedback'])) ?>
                                <?php endif; ?>
                            </td>

                            <td class="status" id="desktop-status-<?= $row['reservation_id'] ?>" data-carpool-id="<?= $row['reservation_id'] ?>">
                                <?php if ($row['status'] === 'pending'): ?>
                                    <!-- Form for "Start Trip" on desktop -->
                                    <form method="POST" action="update_reservation_status.php">
                                        <input type="hidden" name="reservation_id" value="<?= $row['reservation_id'] ?>">
                                        <input type="hidden" name="driver_id" value="<?= $row['driver_id'] ?>">
                                        <button type="button" class="btn-start-trip btn-cancel-history btn-cancel">Démarrer le trajet</button>
                                    </form>
                                <?php elseif ($row['status'] === 'started'): ?>
                                    <!-- Text for "Arrivé à destination" -->
                                    <p style="color:var(--dark-green); font-weight: 550">Arrivé à destination</p>
                                <?php else: ?>
                                    <!-- Text for "Terminé" -->
                                    <span style="color: grey;">Terminé</span>
                                <?php endif; ?>
                            </td>


                            <td id="action-<?= $row['reservation_id'] ?>">
                                <?php if ($row['status'] !== 'finished'): ?>
                                    <form method="POST" action="cancelReservation.php" class="cancel-form"
                                          onsubmit="return confirm('Êtes-vous sûr de vouloir annuler ce covoiturage?');">
                                        <input type="hidden" name="reservation_id" value="<?= htmlspecialchars($row['reservation_id'] ?? '') ?>">
                                        <button type="submit" class="btn-cancel btn-cancel-history btn-cancel"
                                                id="cancel-btn-<?= $row['reservation_id'] ?>">Annuler</button>
                                    </form>
                                <?php else: ?>
                                    <span style="color: grey;">C'est déjà fait</span>
                                <?php endif; ?>
                            </td>

                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
    </div>

    <!-- Mobile Table -->
    <div class="container">
        <div class="history-table-container__mobile">

            <table class="history-table__history-page__mobile">

                <tbody>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <th>Driver Name</th>
                        <td><?= htmlspecialchars($row['driver_name']) ?></td>
                    </tr>
                    <tr>
                        <th>Price</th>
                        <td><?= htmlspecialchars($row['driver_price']) ?></td>
                    </tr>
                    <tr>
                        <th>Departure Time</th>
                        <td><?= htmlspecialchars($row['departure_time']) ?></td>
                    </tr>
                    <tr>
                        <th>Date</th>
                        <td><?= htmlspecialchars($row['date']) ?></td>
                    </tr>

                    <tr>
                        <th>Eco-Friendly</th>
                        <td><?= isset($row['eco_friendly']) ? ($row['eco_friendly'] ? "Yes" : "No") : "N/A" ?></td>
                    </tr>
                    <tr>
                        <th>Passengers</th>
                        <td><?= htmlspecialchars($row['passenger'] ?? '0') ?></td>
                    </tr>
                    <tr>
                        <th>Credits Earned</th>
                        <td><?= isset($row['eco_friendly']) ? ($row['eco_friendly'] ? 5 : 2) : 0 ?></td>

                    </tr>
                    <tr>
                        <th>Reservation Date</th>
                        <td><?= htmlspecialchars($row['reservation_date'] ?? 'N/A') ?></td>
                    </tr>

                   <tr>
                    <th>Feedback</th>
                    <td> <?php if (empty($row['feedback'])): ?>
                            <!-- Button to go to feedback form if feedback is empty -->
                            <form method="GET" action="feedback_form.php">
                                <input type="hidden" name="reservation_id" value="<?= $row['reservation_id'] ?>">
                                <button type="submit" class="btn-feedback btn-cancel btn-cancel-history">Laisser un avis</button>
                            </form>
                        <?php else: ?>
                            <!-- Display the feedback if already provided -->
                            <?= nl2br(htmlspecialchars($row['feedback'])) ?>
                        <?php endif; ?>
                    </td>
                   </tr>




                    <tr>
                        <th>Status</th>
                        <td class="status" id="mobile-status-<?= $row['reservation_id'] ?>" data-reservation-id="<?= $row['reservation_id'] ?>">
                            <?php if ($row['status'] === 'pending'): ?>
                                <!-- Form for "Start Trip" on mobile -->
                                <form method="POST" action="update_reservation_status.php">
                                    <input type="hidden" name="reservation_id" value="<?= $row['reservation_id'] ?>">
                                    <input type="hidden" name="driver_id" value="<?= $row['driver_id'] ?>">
                                    <button type="button" class="btn-start-trip btn-cancel-history btn-cancel">Démarrer le trajet</button>
                                </form>
                            <?php elseif ($row['status'] === 'started'): ?>
                                <!-- Text for "Arrivé à destination" -->
                                <p style="color:var(--dark-green); font-weight: 550">Arrivé à destination</p>
                            <?php else: ?>
                                <!-- Text for "Terminé" -->
                                <span style="color: grey;">Terminé</span>
                            <?php endif; ?>
                        </td>
                    </tr>


                    <tr>
                        <th>Action</th>

                        <td id="action-<?= $row['reservation_id'] ?>" class="mobile-action">
                            <?php if ($row['status'] !== 'finished'): ?>
                                <form method="POST" action="cancelReservation.php" class="cancel-form"
                                      onsubmit="return confirm('Êtes-vous sûr de vouloir annuler ce covoiturage?');">

                                    <input type="hidden" name="reservation_id" value="<?= htmlspecialchars($row['reservation_id'] ?? '') ?>">
                                    <button type="submit" class="btn-cancel btn-cancel-history btn-cancel" id="cancel-btn-<?= $row['reservation_id'] ?>">Annuler</button>
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
<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll('.btn-start-trip').forEach(button => {
            button.addEventListener('click', function (event) {
                event.preventDefault(); // Prevent form submission

                let reservationId = this.closest("form").querySelector("input[name='reservation_id']").value;
                let formData = new FormData();
                formData.append("reservation_id", reservationId);

                fetch("update_reservation_status.php", {
                    method: "POST",
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update button text
                            this.textContent = "Arrivé à destination";
                            this.style.color = "green";

                            // Update status text
                            let statusCell = document.querySelector(`#desktop-status-${reservationId}`);
                            if (statusCell) {
                                statusCell.innerHTML = '<p style="color: var(--dark-green); font-weight: 550;">Arrivé à destination</p>';
                            }

                            let mobileStatusCell = document.querySelector(`#mobile-status-${reservationId}`);
                            if (mobileStatusCell) {
                                mobileStatusCell.innerHTML = '<p style="color: var(--dark-green); font-weight: 550;">Arrivé à destination</p>';
                            }

                            // Change cancel button text
                            let cancelButton = document.querySelector(`#cancel-btn-${reservationId}`);
                            if (cancelButton) {
                                cancelButton.parentElement.innerHTML = "<span style='color: grey;'>C'est déjà fait</span>";
                            }
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch(error => console.error("Erreur:", error));
            });
        });
    });

</script>

<script src="../public/assets/script/app.js"></script>

</body>
</html>

