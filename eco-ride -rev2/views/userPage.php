<?php
session_start();

if (isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
     // Additional content for the user
} else {
    // Redirect to login if no session exists
    header("Location: loginPage.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - EcoRide</title>
    <link rel="stylesheet" href="../public/assets/styles/reset.css">
    <link rel="stylesheet" href="../public/assets/styles/fonts.css">
    <link rel="stylesheet" href="../public/assets/styles/grid.css">
    <link rel="stylesheet" href="../public/assets/styles/app.css">
    <link rel="stylesheet" href="../public/assets/styles/responsive.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>
<body>
<header class="header">
    <div class="nav">
        <div class="logo-container">
            <a href="index.html" class="app-logo">
                <img src="../public/assets/images/png/logo.png" alt="logo image" class="app-logo__img">
            </a>
            <span class="logo-container__text">ECO RIDE</span>
        </div>
        <div class="menu-icon__wrapper">
            <ul class="menu">
                <li class="menu__item">
                    <a href="#" class="menu__link">Dashboard</a>
                </li>
                <li class="menu__item">
                    <a href="historyPage.php" class="menu__link">Reservation History</a>
                </li>
                <li class="menu__item">
                    <a href="#" class="menu__link">Credits</a>
                </li>
                <li class="menu__item">
                    <a href="logout.php" class="menu__link">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</header>

<main class="main">
    <div class="container user-dashboard">
        <!-- Welcome Message -->
        <section class="welcome">
            <h1 class="welcome__text">
                <?php
                echo "Welcome back, " . htmlspecialchars($user['full_name']) . "!"; ?>
            </h1>
            <p>What would you like to do today?</p>
        </section>

        <!-- Dashboard Actions -->
        <section class="dashboard-actions">
            <div class="action-card">
                <i class="fa-solid fa-car action-card__icon"></i>
                <h3>Reserve a Car</h3>
                <a href="#" class="action-card__btn">Reserve Now</a>
            </div>
            <div class="action-card">
                <i class="fa-solid fa-clock-rotate-left action-card__icon"></i>
                <h3>View Reservation History</h3>
                <a href=#" class="action-card__btn">View History</a>
            </div>
            <div class="action-card">
                <i class="fa-solid fa-wallet action-card__icon"></i>
                <h3>Check Credits</h3>
                <a href="#" class="action-card__btn">View Balance</a>
            </div>
        </section>

        <!-- History Section -->
        <section class="history">
            <h2>Recent Reservations</h2>
            <table class="history-table">
                <thead>
                <tr>
                    <th>Date</th>
                    <th>Car Model</th>
                    <th>Pickup Location</th>
                    <th>Dropoff Location</th>
                    <th>Price</th>
                </tr>
                </thead>
                <tbody id="reservationHistory">
                <tr>
                    <td>2025-01-01</td>
                    <td>Toyota Prius</td>
                    <td>Paris Center</td>
                    <td>Airport</td>
                    <td>€50</td>
                </tr>
                <!-- Additional rows will be dynamically added with PHP or JS -->
                </tbody>
            </table>
        </section>

        <!-- Credit Balance Section -->
        <section class="credits">
            <h2>Your Credits</h2>
            <div class="credits__container">
                <i class="fa-solid fa-coins credits__icon"></i>
                <p class="credits__balance"><span id="userCredits">€100</span> Available</p>
            </div>
        </section>
    </div>
</main>

<footer class="footer">
    <div class="container">
        <div class="footer-container">
            <div class="footer-menu">
                <a href="#">Dashboard</a>
                <a href="historyPage.php">Reservation History</a>
                <a href="#">Credits</a>
                <a href="logout.php">Logout</a>
            </div>
            <div class="footer-logo">
                <img src="../public/assets/images/png/logo.png" alt="Eco Ride Logo">
                <span class="logo-container__text">Eco Ride</span>
            </div>
        </div>
        <div class="footer-bottom">
            © 2025 Eco Ride. All Rights Reserved.
        </div>
    </div>
</footer>

<script>
    // Replace placeholders with actual user data (if using JavaScript to fetch data)
    document.getElementById("userName").innerText = "John Doe"; // Example
    document.getElementById("userCredits").innerText = "€75";   // Example
</script>
</body>
</html>


