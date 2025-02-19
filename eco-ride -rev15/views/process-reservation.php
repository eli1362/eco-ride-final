<?php
global $db, $availableDriver;
session_start();
include_once '../config/Database.php'; // Adjust the path to your database configuration file

// Initialize variables to avoid undefined key warnings
$depart = $destination = $date = $time = $passenger_count = $carType = "";

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form inputs with validation
    $depart = isset($_POST['depart']) ? $_POST['depart'] : "";
    $destination = isset($_POST['destination']) ? $_POST['destination'] : "";
    $date = isset($_POST['date']) ? $_POST['date'] : "";
    $time = isset($_POST['time']) ? $_POST['time'] : "";
    $passenger_count = isset($_POST['passenger']) ? (int)$_POST['passenger'] : 0;
    $carType = isset($_POST['carType']) ? $_POST['carType'] : "";

    // Check if the user is logged in
    if (!isset($_SESSION['user_id'])) {
        die("You must be logged in to make a reservation.");
    }
    $user_id = $_SESSION['user_id'];

    // Validate required inputs
    if (empty($date) || empty($time) || $passenger_count <= 0) {
        die("Invalid input: Please provide all required fields (date, time, passenger count).");
    }

    // Fetch available drivers for the exact date and time
    $sqlExact = "
        SELECT * FROM drivers
        WHERE date = ? AND departure_time = ? AND remaining_seats >= ?
    ";
    $stmtExact = $db->prepare($sqlExact);
    $stmtExact->bind_param("ssi", $date, $time, $passenger_count); // 'ssi' = string, string, integer
    $stmtExact->execute();
    $resultExact = $stmtExact->get_result();

    // Check if any drivers match the exact criteria
    if ($resultExact->num_rows > 0) {
        echo "<h3>Available Drivers:</h3>";
        while ($row = $resultExact->fetch_assoc()) {
            echo "Driver: " . htmlspecialchars($row['name']) . "<br>";
            echo "Remaining Seats: " . htmlspecialchars($row['remaining_seats']) . "<br>";
            echo "Price: " . htmlspecialchars($row['price']) . "<br>";
            echo "Eco-friendly: " . ($row['eco_friendly'] ? "Yes" : "No") . "<br><br>";
        }
    } else {
        // Fetch alternative drivers (nearest date and time)
        $sqlAlternative = "
            SELECT * FROM drivers
            WHERE remaining_seats >= ?
            ORDER BY ABS(TIMESTAMPDIFF(MINUTE, CONCAT(date, ' ', departure_time), CONCAT(?, ' ', ?))) ASC
            LIMIT 5
        ";
        $stmtAlternative = $db->prepare($sqlAlternative);
        $stmtAlternative->bind_param("iss", $passenger_count, $date, $time); // 'iss' = integer, string, string
        $stmtAlternative->execute();
        $resultAlternative = $stmtAlternative->get_result();

        // Display alternative drivers
        if ($resultAlternative->num_rows > 0) {
            echo "<h3>No exact matches found. Here are some alternative drivers:</h3>";
            while ($row = $resultAlternative->fetch_assoc()) {
                echo "Driver: " . htmlspecialchars($row['name']) . "<br>";
                echo "Date: " . htmlspecialchars($row['date']) . "<br>";
                echo "Departure Time: " . htmlspecialchars($row['departure_time']) . "<br>";
                echo "Remaining Seats: " . htmlspecialchars($row['remaining_seats']) . "<br>";
                echo "Price: " . htmlspecialchars($row['price']) . "<br>";
                echo "Eco-friendly: " . ($row['eco_friendly'] ? "Yes" : "No") . "<br><br>";
            }
        } else {
            echo "<h3>No drivers available for the selected criteria or nearby options.</h3>";
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
    <div class="driver-result__wrapper">


    <!-- The reservation form -->
    <form class="car-rental-form" method="POST" action="process-reservation.php">

        <div class="form-group">
            <i class="fa-solid fa-calendar-days icon"></i>
            <label for="date" class="label">Aujourd'hui</label>
            <input type="date" name="date" id="date" class="form-control"  value="<?php echo htmlspecialchars($date); ?>">
            <span class="line"></span>
        </div>

        <div class="form-group">
            <i class="fa-solid fa-clock icon"></i>
            <label for="time" class="label">Heure de départ</label>
            <input type="time" name="time" class="form-control" id="time" value="<?php echo htmlspecialchars($time); ?>">
            <span class="line"></span>
        </div>

        <div class="form-group form-group-btn">
        <button type="submit" class="search-btn">Submit</button>
        </div>
    </form>

        <!-- Display the driver result -->
        <?php if ($availableDriver !== null): ?>
            <div class="driver-info">
                <img src="<?php echo $availableDriver['photo']; ?>" alt="Driver Photo" class="driver-photo">
                <div class="driver-details">
                    <h2>" <?php echo $availableDriver['name']; ?> "</h2>
                    <p class="driverinfo-title">Rating: <span class="driverInfo-span"> <?php echo $availableDriver['rating']; ?> </span> ⭐</p>
                    <p class="driverinfo-title">Price: <span class="driverInfo-span"> $<?php echo $availableDriver['price']; ?></span></p>
                    <p class="driverinfo-title">Departure Time: <span class="driverInfo-span"> <?php echo $availableDriver['departure_time']; ?></span></p>
                    <p class="driverinfo-title">Date: <span class="driverInfo-span"> <?php echo $availableDriver['date']; ?></span></p>
                    <p class="driverinfo-title">Eco-Friendly Ride: <span class="driverInfo-span"> <?php echo ($availableDriver['eco_friendly'] ? "Yes" : "No"); ?></span></p>

                    <!-- Create the form for reservation -->
                    <form action="reservation.php" method="POST">
                        <input type="hidden" name="driver_id" value="<?php echo $availableDriver['driver_id']; ?>">
                        <input type="hidden" name="driver_name" value="<?php echo $availableDriver['name']; ?>">
                        <input type="hidden" name="driver_price" value="<?php echo $availableDriver['price']; ?>">
                        <input type="hidden" name="driver_departure_time" value="<?php echo $availableDriver['departure_time']; ?>">
                        <input type="hidden" name="driver_date" value="<?php echo $availableDriver['date']; ?>">
                        <input type="hidden" name="driver_eco_friendly" value="<?php echo $availableDriver['eco_friendly']; ?>">
                        <button type="submit" class="search-btn search-btn__driverInfo" id="driverReserve-button">Reserve</button>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <p class="profits__first-title profits__first-title__driverInfo">No drivers available at the selected time.</p>

            <?php if (!empty($alternativeDrivers)): ?>
                <p class="driver-alternative">Alternative drivers available:</p>

                <?php foreach ($alternativeDrivers as $driver): ?>
                    <div class="driver-info">
                        <img src="<?php echo $driver['photo']; ?>" alt="Driver Photo" class="driver-photo">
                        <div class="driver-details">
                            <h2>" <?php echo $driver['name']; ?> "</h2>
                            <p class="driverinfo-title">Rating: <span class="driverInfo-span"><?php echo $driver['rating']; ?></span> ⭐</p>
                            <p class="driverinfo-title">Price: <span class="driverInfo-span">$<?php echo $driver['price']; ?></span></p>
                            <p class="driverinfo-title">Departure Time: <span class="driverInfo-span"><?php echo $driver['departure_time']; ?></span></p>
                            <p class="driverinfo-title">Date: <span class="driverInfo-span"><?php echo $driver['date']; ?></span></p>
                            <p class="driverinfo-title">Eco-Friendly Ride: <span class="driverInfo-span"><?php echo ($driver['eco_friendly'] ? "Yes" : "No"); ?></span></p>

                            <!-- Create the form for reservation -->
                            <form action="reservation.php" method="POST">
                                <input type="hidden" name="driver_id" value="<?php echo $driver['driver_id']; ?>"> <!-- Corrected variable reference -->
                                <input type="hidden" name="driver_name" value="<?php echo $driver['name']; ?>">
                                <input type="hidden" name="driver_price" value="<?php echo $driver['price']; ?>">
                                <input type="hidden" name="driver_departure_time" value="<?php echo $driver['departure_time']; ?>">
                                <input type="hidden" name="driver_date" value="<?php echo $driver['date']; ?>">
                                <input type="hidden" name="driver_eco_friendly" value="<?php echo $driver['eco_friendly']; ?>">
                                <button type="submit" class="search-btn search-btn__driverInfo" id="driverReserve-button">Reserve</button>
                            </form>
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
