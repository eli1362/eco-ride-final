<?php
global $db;
session_start();
include_once '../config/Database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form input values
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if the username already exists
    $checkSql = "SELECT * FROM admins WHERE username = '$username'";
    $checkResult = mysqli_query($db, $checkSql);

    if (mysqli_num_rows($checkResult) > 0) {
        // Username already exists, store an error message
        $_SESSION['error'] = "The username '$username' is already taken. Please choose a different one.";
    } else {
        // Hash the password for security
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert the data into the database
        $sql = "INSERT INTO admins (username, password) VALUES ('$username', '$hashedPassword')";

        if (mysqli_query($db, $sql)) {
            // Store success message in session with a link to login page
            $_SESSION['message'] = "Admin registered successfully! Now you can <a href='loginAdmin.php' style='color: red;font-size: 2rem'>login</a>.";
        } else {
            // Store error message in session
            $_SESSION['error'] = "Error: " . $sql . "<br>" . mysqli_error($db);
        }
    }

    mysqli_close($db);

    // Redirect to avoid form resubmission on refresh
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoRide - Admin Dashboard</title>
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
    <!-- First navbar -->
    <section class="desktopNav-address">
        <div class="desktopNav-address__group desktopNav-address__group-paddingRight">
            <i class="fa-regular fa-clock desktopNav-address__logo"></i>
            <p class="desktopNav-address__text">
                Du lundi au samedi, de 18:00 à minuit
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
                123 Rue Principale, Paris
            </p>
        </div>
    </section>

    <!-- Second navbar menu -->
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
                <li class="menu__item"><a href="userPage1.php" class="menu__link">Dashboard</a></li>
                <li class="menu__item"><a href="history-carpool.php" class="menu__link">Historique des carpooling</a>
                </li>
                <li class="menu__item"><a href="" class="menu__link">Credits</a></li>
                <li class="menu__item"><a href="logoutPage.php" class="menu__link">Logout</a></li>
            </ul>
            <div class="nav-menu">
                <ul class="mobile-menu">
                    <li class="mobile-menu__item"><a href="userPage1.php" class="mobile-menu__link">Dashboard</a></li>
                    <li class="mobile-menu__item"><a href="history-carpool.php" class="mobile-menu__link">Historique des
                            carpooling</a></li>
                    <li class="mobile-menu__item"><a href="logoutPage.php" class="mobile-menu__link">Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</header>

<main style="background:linear-gradient(to bottom, #C8D5B9, #FAFAF0)">
    <div class="container">
        <div class="admin_registration_wrapper">
            <h2 class="login__title">Admin Registration</h2>

            <!-- Display session message or error if exists -->
            <?php if (isset($_SESSION['message'])): ?>
                <div class="success-message" style="color: green; font-size: 1.2rem; margin-bottom: 20px;">
                    <?php
                    echo $_SESSION['message']; // Display success message with link to login
                    unset($_SESSION['message']); // Clear session message after displaying
                    ?>
                </div>
            <?php elseif (isset($_SESSION['error'])): ?>
                <div class="error-message" style="color: red; font-size: 1.2rem; margin-bottom: 20px;">
                    <?php
                    echo $_SESSION['error']; // Display error message
                    unset($_SESSION['error']); // Clear session error after displaying
                    ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required><br><br>

                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required><br><br>

                <button type="submit" class="btn-cancel" style="width: 100%">Register</button>
                <button type="submit" class="btn-cancel" style="width: 100%;margin-top: 1.5rem"><a href="loginAdmin.php"  >Login</a></button>
            </form>
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

</body>
</html>
