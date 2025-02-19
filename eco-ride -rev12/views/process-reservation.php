<?php
// Start the session
session_start();
global $db;
include_once "../config/Database.php";

// Initialize variables to store the form data (in case the page reloads)
$depart = $destination = $date = $time = '';
$availableDriver = null;
$alternativeDrivers = [];


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
}

    // Combine the date and time into a datetime string for comparison or database entry
    $selectedDateTime = $date . ' ' . $time; // Format: YYYY-MM-DD HH:MM

        // Example driver data
    $drivers = [
        [
            'id' => 1,
            'name' => 'John Doe',
            'photo' => '../public/assets/images/image/driver1.jpg',
            'rating' => 4.5,
            'remaining_seats' => 3,
            'price' => 15.00,
            'date' => '2025-01-22',
            'departure_time' => '08:00',
            'arrival_time' => '09:00',
            'eco_friendly' => true,
            'availability' => ['2025-01-22 08:00', '2025-01-22 09:00'],
        ],
        [
            'id' => 2,
            'name' => 'Jane Smith',
            'photo' => '../public/assets/images/image/driver2.jpg',
            'rating' => 4.8,
            'remaining_seats' => 2,
            'price' => 20.00,
            'date' => '2025-01-22',
            'departure_time' => '09:30',
            'arrival_time' => '11:00',
            'eco_friendly' => false,
            'availability' => ['2025-01-22 09:30', '2025-01-22 11:00'],
        ],
        [
            'id' => 3,
            'name' => 'Alex Green',
            'photo' => '../public/assets/images/image/driver3.jpg',
            'rating' => 5.0,
            'remaining_seats' => 4,
            'price' => 18.00,
            'date' => '2025-01-22',
            'departure_time' => '11:30',
            'arrival_time' => '13:00',
            'eco_friendly' => true,
            'availability' => ['2025-01-22 11:30', '2025-01-22 13:00'],
        ],
        [
            'id' => 4,
            'name' => 'Emily White',
            'photo' => '../public/assets/images/image/driver11.jpg',
            'rating' => 4.2,
            'remaining_seats' => 3,
            'price' => 22.00,
            'date' => '2025-01-22',
            'departure_time' => '13:30',
            'arrival_time' => '15:30',
            'eco_friendly' => false,
            'availability' => ['2025-01-22 13:30', '2025-01-22 15:00'],
        ],
        [
            'id' => 5,
            'name' => 'Michael Brown',
            'photo' => '../public/assets/images/image/driver5.jpg',
            'rating' => 4.7,
            'remaining_seats' => 2,
            'price' => 19.00,
            'date' => '2025-01-22',
            'departure_time' => '15:30',
            'arrival_time' => '17:00',
            'eco_friendly' => true,
            'availability' => ['2025-01-22 15:30', '2025-01-22 17:00'],
        ],
        [
            'id' => 6,
            'name' => 'Sarah Taylor',
            'photo' => '../public/assets/images/image/driver7.jpg',
            'rating' => 4.3,
            'remaining_seats' => 4,
            'price' => 16.00,
            'date' => '2025-01-22',
            'departure_time' => '17:30',
            'arrival_time' => '19:00',
            'eco_friendly' => false,
            'availability' => ['2025-01-22 17:30', '2025-01-22 19:00'],
        ],
        [
            'id' => 7,
            'name' => 'Chris Wilson',
            'photo' => '../public/assets/images/image/driver15.jpg',
            'rating' => 4.9,
            'remaining_seats' => 3,
            'price' => 17.50,
            'date' => '2025-01-22',
            'departure_time' => '19:30',
            'arrival_time' => '21:00',
            'eco_friendly' => true,
            'availability' => ['2025-01-22 19:30', '2025-01-22 21:00'],
        ],
        [
            'id' => 8,
            'name' => 'Jessica Brown',
            'photo' => '../public/assets/images/image/driver14.jpg',
            'rating' => 4.1,
            'remaining_seats' => 5,
            'price' => 21.00,
            'date' => '2025-01-22',
            'departure_time' => '21:30',
            'arrival_time' => '23:00',
            'eco_friendly' => false,
            'availability' => ['2025-01-22 21:30', '2025-01-22 23:00'],
        ],
        [
            'id' => 9,
            'name' => 'Daniel Garcia',
            'photo' => '../public/assets/images/image/driver14.jpg',
            'rating' => 4.6,
            'remaining_seats' => 2,
            'price' => 20.00,
            'date' => '2025-01-23',
            'departure_time' => '17:00',
            'arrival_time' => '18:30',
            'eco_friendly' => true,
            'availability' => ['2025-01-23 17:00', '2025-01-23 18:30'],
        ],
        [
            'id' => 10,
            'name' => 'Laura Martinez',
            'photo' => '../public/assets/images/image/driver12.jpg',
            'rating' => 4.4,
            'remaining_seats' => 3,
            'price' => 18.50,
            'date' => '2025-01-23',
            'departure_time' => '18:00',
            'arrival_time' => '19:00',
            'eco_friendly' => false,
            'availability' => ['2025-01-23 18:00', '2025-01-23 19:00'],
        ],
    ];

// Array to track driver IDs that have been added to the alternatives
$addedDriverIds = [];

// Find drivers who match the selected date and time
foreach ($drivers as $driver) {
    $driverAvailability = $driver['availability'];

    foreach ($driverAvailability as $availability) {
        if ($selectedDateTime === $availability) {
            $availableDriver = $driver;
            break;
        } else if (abs(strtotime($selectedDateTime) - strtotime($availability)) <= 3600) {
            // Check if the driver is already added to the alternatives
            if (!in_array($driver['id'], $addedDriverIds)) {
                $alternativeDrivers[] = $driver;
                $addedDriverIds[] = $driver['id']; // Mark this driver as added
            }
        }
    }

    if ($availableDriver !== null) {
        break;
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
            <i class="fa-solid fa-location-dot icon"></i>
            <label for="depart" class="label">Départ</label>
            <input type="text" name="depart" class="form-control" id="depart" value="<?php echo htmlspecialchars($depart); ?>" placeholder="Departure">
            <span class="line" ></span>
        </div>

        <div class="form-group">
            <i class="fa-solid fa-location-dot icon"></i>
            <label for="destination" class="label">Destination</label>
            <input type="text" name="destination" id="destination" class="form-control" value="<?php echo htmlspecialchars($destination); ?>" placeholder="Destination">
            <span class="line"></span>
        </div>

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
                <button type="submit" class="search-btn search-btn__driverInfo">Reserve</button>
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
                        <p class="driverinfo-title" >Rating: <span class="driverInfo-span"><?php echo $driver['rating']; ?></span> </p>
                        <p class="driverinfo-title">Price: <span  class="driverInfo-span">$<?php echo $driver['price']; ?></span> ⭐ </p>
                        <p class="driverinfo-title">Departure Time: <span class="driverInfo-span"> <?php echo $driver['departure_time']; ?></span> </p>
                        <p class="driverinfo-title">Date: <span class="driverInfo-span"> <?php echo $driver['date']; ?></span> </p>
                        <p class="driverinfo-title">Eco-Friendly Ride: <span class="driverInfo-span"> <?php echo ($driver['eco_friendly'] ? "Yes" : "No"); ?></span> </p>
                        <button type="submit" class="search-btn search-btn__driverInfo">Reserve</button>
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
