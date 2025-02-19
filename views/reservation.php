<?php
global $db;
session_start();
include_once '../config/Database.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = "You must be logged in to make a reservation.";
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id']; // Logged-in user

// Initialize variables
$driver_name = $passenger = $date = $departure_time = $reservation_id = "";

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $driver_id = $_POST['driver_id'] ?? 0;
    $passenger = $_POST['passenger'] ?? 0;
    $driver_price = $_POST['driver_price'] ?? "";
    $departure_time = $_POST['departure_time'] ?? "";
    $date = $_POST['date'] ?? "";
    $eco_friendly = isset($_POST['eco_friendly']) ? (int)$_POST['eco_friendly'] : 0;

// Validate required fields
    if ($driver_id <= 0 || $passenger <= 0) {
        $_SESSION['error_message'] = "Invalid driver ID or passenger count.";
        header("Location: index.php");
        exit();
    }
    if (empty($driver_price) || empty($departure_time) || empty($date)) {
        $_SESSION['error_message'] = "Missing driver details.";
        header("Location: index.php");
        exit();
    }

// Get the driver's full name from the `drivers` table
    $sqlGetDriverName = "SELECT name FROM drivers WHERE driver_id = ?";
    $stmtGetDriverName = $db->prepare($sqlGetDriverName);
    $stmtGetDriverName->bind_param("i", $driver_id);
    $stmtGetDriverName->execute();
    $resultDriverName = $stmtGetDriverName->get_result();
    if ($rowDriver = $resultDriverName->fetch_assoc()) {
        $driver_name = $rowDriver['name'];
    } else {
        $_SESSION['error_message'] = "Driver not found.";
        header("Location: index.php");
        exit();
    }

// Get the user name from the `users` table
    $sqlGetUserName = "SELECT full_name FROM users WHERE user_id = ?";
    $stmtGetUserName = $db->prepare($sqlGetUserName);
    $stmtGetUserName->bind_param("i", $user_id);
    $stmtGetUserName->execute();
    $resultUserName = $stmtGetUserName->get_result();
    $user_name = "";
    if ($rowUser = $resultUserName->fetch_assoc()) {
        $user_name = $rowUser['full_name'];
    }

// Check if the driver exists in the `users` table using LIKE for flexibility
    $sqlCheckDriverInUsers = "SELECT user_id FROM users WHERE full_name LIKE ?";
    $stmtCheckDriverInUsers = $db->prepare($sqlCheckDriverInUsers);
    $driver_name_with_wildcards = "%" . $driver_name . "%"; // Add wildcards to allow for partial matching
    $stmtCheckDriverInUsers->bind_param("s", $driver_name_with_wildcards);
    $stmtCheckDriverInUsers->execute();
    $resultCheckDriverInUsers = $stmtCheckDriverInUsers->get_result();
    $driver_is_user = ($resultCheckDriverInUsers->num_rows > 0);

// Log the result for debugging
    error_log("Checking driver: $driver_name - Result: " . ($driver_is_user ? "Found" : "Not Found"));

// Assign credits based on eco-friendliness
    $credits = $eco_friendly ? 5 : 2;

// Insert into reservations table
    $sqlInsertReservation = "
INSERT INTO reservations (user_id, driver_id, driver_name, driver_price, departure_time, date, eco_friendly, passenger, reservation_date)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
";
    $stmtInsertReservation = $db->prepare($sqlInsertReservation);
    $stmtInsertReservation->bind_param("iissssii", $user_id, $driver_id, $driver_name, $driver_price, $departure_time, $date, $eco_friendly, $passenger);

    if ($stmtInsertReservation->execute()) {
        $reservation_id = $db->insert_id;

        // Update driver's remaining seats
        $sqlUpdateSeats = "UPDATE drivers SET remaining_seats = remaining_seats - ? WHERE driver_id = ?";
        $stmtUpdateSeats = $db->prepare($sqlUpdateSeats);
        $stmtUpdateSeats->bind_param("ii", $passenger, $driver_id);
        $stmtUpdateSeats->execute();

        // Update user credits
        $sqlUpdateCredits = "UPDATE users SET credits = credits + ? WHERE user_id = ?";
        $stmtUpdateCredits = $db->prepare($sqlUpdateCredits);
        $stmtUpdateCredits->bind_param("ii", $credits, $user_id);
        $stmtUpdateCredits->execute();

        // If the driver is a registered user, insert into the carpool table
        if ($driver_is_user) {
            $sqlInsertCarpool = "
                INSERT INTO carpool (driver_id, departure_date, departure_time, remaining_seats, price, eco_friendly, user_id, user_name)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ";

            // Bind parameters with correct types (10 variables to match 10 placeholders)
            $stmtInsertCarpool = $db->prepare($sqlInsertCarpool);
            $stmtInsertCarpool->bind_param(
                "issidiis",
                $driver_id,
                $date,
                $departure_time,
                $passenger,
                $driver_price,
                $eco_friendly,
                $user_id,
                $user_name
            );

            // Execute the query to insert into the carpool table
            if ($stmtInsertCarpool->execute()) {
                $_SESSION['success_message'] = "Reservation and carpool details saved successfully.";
            } else {
                $_SESSION['error_message'] = "Unable to save carpool details.";
            }
        }

        // Success message
        $_SESSION['success_message'] = "Your reservation was successful! You earned $credits credits.";
        header("Location: reservation.php?reservation_id=$reservation_id");
        exit();
    } else {
        $_SESSION['error_message'] = "Reservation failed. Please try again.";
        header("Location: index.php");
        exit();
    }
}

// Fetch reservation details if reservation_id exists in the URL
if (isset($_GET['reservation_id'])) {
    $reservation_id = (int)$_GET['reservation_id'];

    $sqlGetReservation = "SELECT * FROM reservations WHERE reservation_id = ? AND user_id = ?";
    $stmtGetReservation = $db->prepare($sqlGetReservation);
    $stmtGetReservation->bind_param("ii", $reservation_id, $user_id);
    $stmtGetReservation->execute();
    $resultGetReservation = $stmtGetReservation->get_result();

    if ($row = $resultGetReservation->fetch_assoc()) {
        $driver_name = $row['driver_name'];
        $passenger = $row['passenger'];
        $date = $row['date'];
        $departure_time = $row['departure_time'];
        $eco_friendly = $row['eco_friendly'];
    }

    $stmtGetReservation->close();
}

// Redirect if the trip is not eco-friendly
if (isset($eco_friendly) && $eco_friendly == 0) {
    header("Location: pollution.php?driver_name=" . urlencode($driver_name) . "&passenger=" . $passenger . "&date=" . $date . "&departure_time=" . urlencode($departure_time) . "&reservation_id=" . $reservation_id);
    exit();
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
        }

        .message {
            font-size: 1.5em;
            text-align: center;
            padding: 20px;
            background: rgba(255, 0, 0, 0.6);
            border: 1px solid #ff0000;
            border-radius: 8px;
            margin-top: 50px;
            color: white;
        }

        .message h2 {
            font-size: 1.8em;
        }

        .message p {
            font-size: 1.2em;
        }

        .driver-card {
            background-color: #fff;
            padding: 20px;
            margin: 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
        }

        .driver-card h4 {
            font-size: 1.5em;
            color: #333;
        }

        .driver-card p {
            color: #555;
        }

        .driver-card .eco-friendly {
            color: green;
        }

        .success-message {
            text-align: center;
            padding: 20px;
            background: #e1f7d5;
            border: 1px solid #a2d6b7;
            border-radius: 8px;
            margin-top: 50px;
            color: #333;
        }

        .success-message h2 {
            font-size: 1.8em;
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

<main style="background:linear-gradient(to bottom, #C8D5B9, #FAFAF0)">
    <div class="container">
        <div class="reservation-photo__wrapper">
            <section class="reservation__wrapper">

                <h1 class="ecoride-prime__title">Process Your Reservation</h1>

                <?php
                if (isset($_SESSION['success_message'])) {
                    echo "<h3 style='color: green; font-weight: bold;'>" . $_SESSION['success_message'] . "</h3>";
                    unset($_SESSION['success_message']);
                }
                if (isset($_SESSION['error_message'])) {
                    echo "<h3 style='color: red; font-weight: bold;'>" . $_SESSION['error_message'] . "</h3>";
                    unset($_SESSION['error_message']);
                }
                ?>

                <?php if (!empty($driver_name) && !empty($date) && !empty($departure_time) && !isset($_SESSION['success_message'])) : ?>

                    <!-- Available Electric Drivers Section -->
                    <h3 class="ecoride-prime__title" style="margin: 3rem">Available Electric Drivers:</h3>

                    <!-- Reservation Confirmation for Electric Car -->
                    <h3 style="font-family: 'Montserrat-semiBold',serif;margin-top: 2rem" class="driver-info">
                        You reserved the driver: <strong
                                style="color: var(--black-text)"><?= htmlspecialchars($driver_name); ?></strong><br><br>
                        With: <strong style="color: var(--black-text)"><?= htmlspecialchars($passenger); ?>
                            passengers</strong> <br><br>
                        On: <strong style="color: var(--black-text)"><?= htmlspecialchars($date); ?></strong><br><br>
                        At: <strong
                                style="color: var(--black-text)"><?= htmlspecialchars($departure_time); ?></strong><br><br>
                        <h4 style="color: var(--dark-green);font-family: 'Montserrat-semiBold', serif;margin-bottom: 2rem">
                            If for any reason you don't want it, you can cancel your travel by clicking this button.
                        </h4>
                        <a href="cancelReservation.php?id=<?= htmlspecialchars($reservation_id); ?>" class="btn-cancel">Cancel</a>
                    </h3>

                <?php else : ?>

                    <p>No reservation found.</p>

                <?php endif; ?>


            </section>
            <div class="ecoride-prime__photo">
                <img alt="car png" src="../public/assets/images/png/Car%20rental-bro.png" class="ecoride-prime__img">
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
</html>