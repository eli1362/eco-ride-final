<?php

global $db;
session_start();
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
                $_SESSION['user_id'] = $user['id']; // Store the user ID in the session
                $_SESSION['user'] = $user;  // Store the user data (optional)

                // Redirect to the user page after successful login
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
    <!-- First navbar omitted for brevity -->
</header>

<main class="login__wrapper">
    <section class="container-login">
        <div class="login-container">
            <h2 class="login__title">Insérez votre email et votre mot de passe !!</h2>
            <form class="login-form" id="login-form" method="POST" action="loginPage.php">
                <!-- Email -->
                <div class="login-form-group">
                    <label for="email">Email</label>
                    <input
                            type="email"
                            id="email"
                            name="email"
                            value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                            required>
                    <!-- Error for email -->
                    <?php if (!empty($errors['email'])): ?>
                        <div class="error-message">
                            <?= $errors['email']; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Password -->
                <div class="login-form-group">
                    <label for="password">Mot de passe</label>
                    <input
                            type="password"
                            id="password"
                            name="password"
                            value=""
                            required>
                    <!-- Error for password -->
                    <?php if (!empty($errors['password'])): ?>
                        <div class="error-message">
                            <?= $errors['password']; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="login-form-options">
                    <a href="forgotPasswordPage.php" class="forgot-password">Mot de passe oublié ?</a>
                </div>

                <button type="submit" class="login-btn">Login</button>
            </form>

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
        <div class="footer-bottom">
            © 2024 Your Company. All Rights Reserved
        </div>
    </div>
</footer>

<script src="../public/assets/script/app.js"></script>
</body>
</html>

<?php
// Clear error messages and old input after the page is rendered
unset($_SESSION['errors']);
unset($_SESSION['old']);
?>
