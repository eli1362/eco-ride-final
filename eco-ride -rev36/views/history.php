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
    SELECT r.driver_name, r.driver_price, r.departure_time, r.date, r.eco_friendly, r.passenger, r.reservation_date
    FROM reservations r
    WHERE r.user_id = ?
    ORDER BY r.reservation_date DESC
";
$stmtFetchHistory = $db->prepare($sqlFetchHistory);
$stmtFetchHistory->bind_param("i", $user_id);
$stmtFetchHistory->execute();
$resultHistory = $stmtFetchHistory->get_result();



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
            <h1 class="ecoride-prime__title" style="text-align: center">Your Reservation History</h1>

            <?php
            if ($resultHistory->num_rows === 0) {
                echo "<p class='no-history'>You have no reservation history.</p>";
            } else {
                echo "<table class='history-table__history-page'>";
                echo "<thead>
            <tr>
                <th>Driver Name</th>
                <th>Price</th>
                <th>Departure Time</th>
                <th>Date</th>
                <th>Eco-Friendly</th>
                <th>Passengers</th>
                <th>Credits Earned</th>
                <th>Reservation Date</th>
            </tr>
        </thead>";
                echo "<tbody>";

                while ($row = $resultHistory->fetch_assoc()) {
                    $credits = $row['eco_friendly'] ? 5 : 2;

                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['driver_name']) . "</td>";
                    echo "<td>$" . htmlspecialchars($row['driver_price']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['departure_time']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['date']) . "</td>";
                    echo "<td>" . ($row['eco_friendly'] ? "Yes" : "No") . "</td>";
                    echo "<td>" . htmlspecialchars($row['passenger']) . "</td>";
                    echo "<td>" . $credits . "</td>";
                    echo "<td>" . htmlspecialchars($row['reservation_date']) . "</td>";
                    echo "</tr>";
                }

                echo "</tbody>";
                echo "</table>";
            }
            ?>
            <div class="history-table-container__mobile">
                <table class="history-table__history-page__mobile">
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
                    </tr>
                    </thead>
                    <tbody>
                    <?php while ($row = $resultHistory->fetch_assoc()) {
                        $credits = $row['eco_friendly'] ? 5 : 2; ?>
                        <tr>
                            <td data-label="Driver Name"><?php echo htmlspecialchars($row['driver_name']); ?></td>
                            <td data-label="Price">$<?php echo htmlspecialchars($row['driver_price']); ?></td>
                            <td data-label="Departure Time"><?php echo htmlspecialchars($row['departure_time']); ?></td>
                            <td data-label="Date"><?php echo htmlspecialchars($row['date']); ?></td>
                            <td data-label="Eco-Friendly"><?php echo ($row['eco_friendly'] ? "Yes" : "No"); ?></td>
                            <td data-label="Passengers"><?php echo htmlspecialchars($row['passenger']); ?></td>
                            <td data-label="Credits Earned"><?php echo $credits; ?></td>
                            <td data-label="Reservation Date"><?php echo htmlspecialchars($row['reservation_date']); ?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
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

