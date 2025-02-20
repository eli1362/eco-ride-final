<?php
global $db;
session_start();
include_once '../config/Database.php';

// Fetch role_id filter if provided, default to '2' (Employee/Driver) if not
$role_id = isset($_GET['role_id']) ? $_GET['role_id'] : 2;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role_id = $_POST['role_id'];
}

// Query to fetch users and drivers
$sql = "SELECT 
            u.user_id, u.full_name, u.email, u.role_id, r.role_name, 
            d.driver_id, d.name AS driver_name, d.model, d.rating 
        FROM roles r
        LEFT JOIN users u ON u.role_id = r.role_id
        LEFT JOIN drivers d ON d.role_id = r.role_id";

// Add filter for role_id
if ($role_id) {
    $sql .= " WHERE r.role_id = ?";
}

$stmt = $db->prepare($sql);
if ($role_id) {
    $stmt->bind_param("i", $role_id);
}
$stmt->execute();
$result = $stmt->get_result();
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

<main style="background:linear-gradient(to bottom, #C8D5B9, #FAFAF0)">
    <div class="container">
        <div class="admin_table_wrapper">
            <h2 class="ecoride-prime__title" style="text-align: center;margin-bottom: 2rem">Manage Users and
                Employees</h2>
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="success-message">
                    <?= $_SESSION['success_message']; ?>
                </div>
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="error-message">
                    <?= $_SESSION['error_message']; ?>
                </div>
                <?php unset($_SESSION['error_message']); ?>
            <?php endif; ?>

            <!-- Admin Filter Form for Role Selection -->
            <form method="POST" action="view_users_employees.php" style="margin:1.5rem auto ">
                <div class="form_label_wrapper">
                    <div class="label_wrapper">
                        <label for="role_id">Filter by Role: </label>
                        <select name="role_id" id="role_id">
                            <option value="2" <?php echo ($role_id == 2) ? 'selected' : ''; ?>>Employee (Driver)
                            </option>
                            <option value="3" <?php echo ($role_id == 3) ? 'selected' : ''; ?>>User</option>
                        </select>
                    </div>
                    <button type="submit" class="search-btn search_btn_admin">Filter</button>
                </div>
            </form>

            <!-- Table Display -->
            <table class="history-table__history-page admin_view_users">
                <thead>
                <tr>
                    <?php if ($role_id == 2): // Driver Table ?>
                        <th>Name</th>
                        <th>Rating</th>
                        <th>Model</th>
                        <th>Role</th>
                        <th>Actions</th>
                    <?php else: // User Table ?>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Actions</th>
                    <?php endif; ?>
                </tr>
                </thead>
                <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <?php if ($role_id == 2): // Driver Data ?>
                            <td><?php echo htmlspecialchars($row['driver_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['rating']); ?></td>
                            <td><?php echo htmlspecialchars($row['model']); ?></td>
                            <td><?php echo htmlspecialchars($row['role_name']); ?></td>
                        <?php else: // User Data ?>
                            <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>

                            <td><?php echo htmlspecialchars($row['role_name']); ?></td>
                        <?php endif; ?>


                        <td>

                            <?php if ($row['role_name'] == 'employee'): ?>
                                <a href="delete_user.php?driver_id=<?= $row['driver_id'] ?>" class="delete_btn" onclick="return confirm('Are you sure you want to delete this driver?');">Delete</a>



                            <?php else: ?>
                                <a href="delete_user.php?user_id=<?= $row['user_id'] ?>" class="delete_btn" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>

                            <?php endif; ?>
                        </td>


                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>

            <!-- Mobile Table for Users -->
            <div class="history-table-container__mobile">
                <table class="history-table__history-page__mobile">
                    <tbody>
                    <?php foreach ($result as $row): ?>
                        <?php if ($row['role_id'] == 3): // For User ?>
                            <tr>
                                <th>Full Name</th>
                                <td><?= htmlspecialchars($row['full_name']) ?></td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td><?= htmlspecialchars($row['email']) ?></td>
                            </tr>

                            <tr>
                                <th>Role</th>
                                <td><?= htmlspecialchars($row['role_name']) ?></td>
                            </tr>
                            <tr>
                                <th>Actions</th>
                                <td>
                                    <a href="update_user.php?user_id=<?= $row['user_id'] ?>" class="update_btn">Update</a> |
                                    <a href="delete_user.php?user_id=<?= $row['user_id'] ?>" class="delete_btn" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Mobile Table for Drivers (Employees) -->
            <div class="history-table-container__mobile">
                <table class="history-table__history-page__mobile">
                    <tbody>
                    <?php foreach ($result as $row): ?>
                        <?php if ($row['role_id'] == 2): // For Driver (Employee) ?>
                            <tr>
                                <th>Name</th>
                                <td><?= htmlspecialchars($row['driver_name']) ?></td>
                            </tr>
                            <tr>
                                <th>Rating</th>
                                <td><?= htmlspecialchars($row['rating']) ?></td>
                            </tr>
                            <tr>
                                <th>Model</th>
                                <td><?= htmlspecialchars($row['model']) ?></td>
                            </tr>
                            <tr>
                                <th>Role</th>
                                <td><?= htmlspecialchars($row['role_name']) ?></td>
                            </tr>
                            <tr>
                                <th>Actions</th>
                                <td>
                                    <?php if ($row['role_id'] == 2): // Driver (Employee) ?>
                                        <a href="update_user.php?driver_id=<?= $row['driver_id'] ?>" class="update_btn">Update</a> |
                                        <a href="delete_user.php?driver_id=<?= $row['driver_id'] ?>" class="delete_btn" onclick="return confirm('Are you sure you want to delete this driver?');">Delete</a>
                                    <?php else: // User ?>
                                        <a href="update_user.php?user_id=<?= $row['user_id'] ?>" class="update_btn">Update</a> |
                                        <a href="delete_user.php?user_id=<?= $row['user_id'] ?>" class="delete_btn" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    </tbody>
                </table>
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


