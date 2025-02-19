<?php

global $db;
session_start();
if (isset($_SESSION['user_id'])) {
    // If user is logged in, redirect them to the homepage or dashboard
    header("Location: index.php"); // or another page
    exit();
}
include '../config/Database.php'; // Include your database connection file

// Initialize error messages
$errors = [
    'email' => '',
    'password' => ''
];

// Initialize old input values
$old = [
    'email' => ''
];

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $old['email'] = $email; // Preserve the email input

    // Validate email and password
    if (empty($email)) {
        $errors['email'] = 'Email is required';
    } elseif (empty($password)) {
        $errors['password'] = 'Password is required';
    } else {
        // Check if email exists in the database
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user) {
            // Check password
            if (password_verify($password, $user['password'])) {
                // Successful login
                $_SESSION['user'] = $user;
                $_SESSION['user_id'] = $user['user_id'];
                header("Location: userPage1.php");
                exit();
            } else {
                // Incorrect password
                $errors['password'] = 'Your password is not correct';
            }
        } else {
            // Email not registered
            $errors['email'] = 'You are not registered. Please register first.';
        }
    }
}


?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoRide - login</title>
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
                <a href="index.php" class="logo-container__text "> ECO RIDE </a>
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
                    <a href="" class="menu__link">Trouver un trajet</a>
                </li>
                <li class="menu__item">
                    <a href="" class="menu__link">Proposer un trajet</a>
                </li>
                <li class="menu__item">
                    <a href="" class="menu__link">À propos de nous</a>
                </li>

            </ul>
            <div class="nav-menu">
                <ul class="mobile-menu">
                    <li class="mobile-menu__item">
                        <a href="" class="mobile-menu__link">Trouver un trajet</a>
                    </li>
                    <li class="mobile-menu__item">
                        <a href="" class="mobile-menu__link">Proposer un trajet</a>
                    </li>
                    <li class="mobile-menu__item">
                        <a href="" class="mobile-menu__link">Rechercher</a>
                    </li>
                    <li class="mobile-menu__item">
                        <a href="" class="mobile-menu__link">Nous contacter</a>
                    </li>
                    <li class="mobile-menu__item">
                        <a href="" class="mobile-menu__link">À propos de nous</a>
                    </li>

                </ul>
                <div id="mobile-menu" class="mobile-dropdown-menu">
                    <a href="loginPage.php" class="mobile-dropdown-item">Connection</a>
                    <a href="registerPage.php" class="mobile-dropdown-item mobile-dropdown-item--margin">Inscription</a>
                </div>

            </div>
            <div class="icon-dropdown">
                <div class="icon-dropdown-icon__container" id="toggleDropdown">
                    <i class="fa-solid fa-user icon-dropdown__icon"></i>
                    <i class="fa-solid fa-plus icon-dropdown__icon--plus" id="toggleIcon"></i>
                </div>
                <div id="menu" class="dropdown-menu">
                    <a href="loginPage.php" class="dropdown-item">Connection</a>
                    <a href="registerPage.php" class="dropdown-item">Inscription</a>
                </div>
            </div>
            <div class="nav__btn">
                <span class="nav__btn-line"></span>
            </div>
        </div>

    </div>

</header>
<main class="login__wrapper">
    <section class="container-login">
        <div class="login-container">
            <h2 class="login__title">insérez votre email et votre mot de passe !!</h2>



            <form class="login-form" id="login-form" method="POST" action="loginPage.php">
                <!-- Email -->
                <div class="login-form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>" required>
                    <div id="email-error" class="error-message">
                        <!-- This will display error for email if any -->
                        <?php if (!empty($errors['email'])): ?>
                            <?= $errors['email']; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Password -->
                <div class="login-form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                    <div id="password-error" class="error-message">
                        <!-- This will display error for password if any -->
                        <?php if (!empty($errors['password'])): ?>
                            <?= $errors['password']; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <button type="submit" class="login-btn">Login</button>
            </form>

            <!-- Display error message if login fails -->
            <?php if (isset($_SESSION['errors']['login'])): ?>
                <div class="error-message message-login">
                    <?= $_SESSION['errors']['login']; ?>
                </div>
            <?php endif; ?>

            <p class="notLogin">
                Vous n'êtes pas encore membre ?<br>
                <a href="registerPage.php" class="register__link">Inscrivez-vous</a>
                    </p>
                </div>
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
<?php
unset($_SESSION['errors']);
unset($_SESSION['old']);
?>