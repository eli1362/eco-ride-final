<?php
global $db;
session_start();
include_once '../config/Database.php';

// Vérifier si l'utilisateur est déjà connecté
if (isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_dashboard.php'); // Rediriger vers le tableau de bord si déjà connecté
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les informations du formulaire
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Vérifier si l'admin existe dans la base de données
    $sql = "SELECT admin_id, username, password FROM admins WHERE username = '$username'";
    $result = mysqli_query($db, $sql);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        // Vérifier si le mot de passe est correct
        if (password_verify($password, $row['password'])) {
            // Connexion réussie
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $row['admin_id'];
            $_SESSION['username'] = $row['username'];

            header('Location:admin_dashboard.php'); // Rediriger vers le tableau de bord
            exit();
        } else {
            $_SESSION['error'] = "Mot de passe incorrect.";
        }
    } else {
        $_SESSION['error'] = "Nom d'utilisateur introuvable.";
    }

    // Fermer la connexion
    mysqli_close($db);
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
            <h2 class="login__title">Connexion Admin</h2>

            <!-- Afficher le message d'erreur, si présent -->
            <?php if (isset($_SESSION['error'])): ?>
                <div class="error-message" style="color: red; font-size: 1.2rem; margin-bottom: 20px;">
                    <?php
                    echo $_SESSION['error']; // Afficher l'erreur
                    unset($_SESSION['error']); // Effacer l'erreur après l'affichage
                    ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <label for="username">Nom d'utilisateur :</label>
                <input type="text" id="username" name="username" required><br><br>

                <label for="password">Mot de passe :</label>
                <input type="password" id="password" name="password" required><br><br>

                <button type="submit" class="btn-cancel" style="width: 100%">Se connecter</button>
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































