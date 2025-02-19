<?php
global $db;
session_start();
include '../config/Database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capture form inputs
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password
    $phone = $_POST['phone_number'];
    $created_by = $_SESSION['user_id'];  // Assuming admin is logged in and their ID is stored in session

    // First, fetch the role_id for 'employee' from the roles table
    $role_query = "SELECT role_id FROM roles WHERE role_name = 'employee' LIMIT 1";
    $role_result = $db->query($role_query);

    if ($role_result && $role_result->num_rows > 0) {
        // Get role_id for 'employee'
        $role_row = $role_result->fetch_assoc();
        $role_id = $role_row['role_id'];  // This will fetch the role_id for the employee role

        // Now prepare and execute the query to insert the employee (using the role_id dynamically)
        $sql = "INSERT INTO users (full_name, email, password, phone_number, role_id, created_by) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ssssii", $full_name, $email, $password, $phone, $role_id, $created_by);

        if ($stmt->execute()) {
            echo "Employee added successfully!";
        } else {
            echo "Error adding employee.";
        }
        $stmt->close();
    } else {
        // If 'employee' role is not found
        echo "Error: 'employee' role not found in the roles table.";
    }
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
                <li class="menu__item"><a href="add_employee.php" class="menu__link">Ajouter Un Utilisateur</a></li>
                <li class="menu__item"><a href="view_users_employees.php" class="menu__link">Gérer Les Utilisateur</a></li>
                <li class="menu__item"><a href="index.php" class="menu__link">Voir Le Site Web</a></li>
                <li class="menu__item"><a href="logout.php" class="menu__link">Déconnexion</a></li>
            </ul>
            <div class="nav-menu">
                <ul class="mobile-menu">
                    <li class="mobile-menu__item"><a href="add_employee.php" class="mobile-menu__link">Ajouter Un Utilisateur</a></li>
                    <li class="mobile-menu__item"><a href="view_users_employees.php" class="mobile-menu__link">Gérer Les Utilisateur</a></li>
                    <li class="mobile-menu__item"><a href="index.php" class="mobile-menu__link">Voir Le Site Web</a></li>
                    <li class="mobile-menu__item"><a href="logout.php" class="mobile-menu__link">Déconnexion</a></li>
                </ul>
            </div>
        </div>
    </div>
</header>

<main style="background:linear-gradient(to bottom, #C8D5B9, #FAFAF0)" class="main">
    <div class="container">
        <!-- Form to add employee -->
        <form method="POST" class="car-rental-form form-roles form_admin">
            <div class="form-group form-group-roles">
                <label class="label" for="full_name">Full Name:</label>
                <input type="text" name="full_name" placeholder="Full Name" required><br>
            </div>

            <div class="form-group form-group-roles">
                <label class="label" for="email">Email:</label>
                <input type="email" name="email" placeholder="Email" required><br>
            </div>

            <div class="form-group form-group-roles">
                <label class="label" for="password">Password:</label>
                <input type="password" name="password" placeholder="Password" required><br>
            </div>

            <div class="form-group form-group-roles">
                <label class="label" for="phone_number">Phone Number:</label>
                <input type="text" name="phone_number" placeholder="Phone Number" required><br>
            </div>

            <button type="submit" class="search-btn">Add Employee</button>
        </form>
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
