<?php
session_start();
if (isset($_SESSION['user_id'])) {
    // If user is logged in, redirect them to the homepage or dashboard
    header("Location: index.php"); // or redirect to a different page
    exit();
}
// Otherwise, show the registration form
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoRide - Register</title>
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
            <h1 class="login__title">
                Créez votre compte
            </h1>
            <form id="register-form" class="login-form" method="POST" action="auth/register.php">
                <!-- Full Name -->
                <div class="login-form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" id="full_name" name="full_name"
                           value="<?= htmlspecialchars($_SESSION['old']['full_name'] ?? '') ?>" required>
                    <?php if (isset($_SESSION['errors']['full_name'])): ?>
                        <span id="full_name_error" class="error-message">
                            <?= htmlspecialchars($_SESSION['errors']['full_name']) ?>
                        </span>
                    <?php endif; ?>
                </div>

                <!-- Email -->
                <div class="login-form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email"
                           value="<?= htmlspecialchars($_SESSION['old']['email'] ?? '') ?>" required>
                    <?php if (isset($_SESSION['errors']['email'])): ?>
                        <span id="email_error" class="error-message">
                            <?= htmlspecialchars($_SESSION['errors']['email']) ?>
                        </span>
                    <?php endif; ?>
                </div>

                <!-- Password -->
                <div class="login-form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                    <?php if (isset($_SESSION['errors']['password'])): ?>
                        <span id="password_error" class="error-message">
                            <?= htmlspecialchars($_SESSION['errors']['password']) ?>
                        </span>
                    <?php endif; ?>
                </div>

                <!-- Confirm Password -->
                <div class="login-form-group">
                    <label for="confirm-password">Confirm Password</label>
                    <input type="password" id="confirm-password" name="confirm-password" required>
                    <?php if (isset($_SESSION['errors']['confirm_password'])): ?>
                        <span id="confirm_password_error" class="error-message">
                            <?= htmlspecialchars($_SESSION['errors']['confirm_password']) ?>
                        </span>
                    <?php endif; ?>
                </div>

                <button type="submit" class="login-btn">Register</button>
            </form>

            <!-- Already have an account -->
            <p class="notLogin">
                Vous avez déjà un compte ?<br> <a href="loginPage.php" class="register__link">Connectez-vous</a>
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
