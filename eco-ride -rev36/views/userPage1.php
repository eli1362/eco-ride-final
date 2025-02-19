<?php
global $db;
session_start(); // Start the session
include_once "../config/Database.php";

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // If not logged in, redirect to login page
    header("Location: loginPage.php");
    exit();
}

// If logged in, you can use the user data
$user = $_SESSION['user']; // Access logged-in user data
$user_id = $user['user_id']; // Access user_id if needed

// Fetch reservation data for the logged-in user
$sql = "SELECT reservation_id, driver_id, driver_price, departure_time, date, eco_friendly, passenger, reservation_date 
        FROM reservations WHERE user_id = ? ORDER BY reservation_date DESC LIMIT 1";
$stmt = $db->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Fetch user's credits (or any related information you need)

$sql = "SELECT credits FROM users WHERE user_id = ?";
$stmt = $db->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_credits = 0;

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $user_credits = $row['credits'];
}

// Passing user's data to JavaScript
echo "<script>var userCredits = $user_credits;</script>";


// Fetch reservation history for the logged-in user
$sql = "SELECT * FROM reservations WHERE user_id = ? ORDER BY reservation_date DESC limit 1";
$stmt = $db->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
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

</head>
<body >
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
                    <a href="index.php" class="menu__link">Accueil</a>
                </li>
                <li class="menu__item">
                    <a href="history.php" class="menu__link">Historique des réservation</a>
                </li>

                <li class="menu__item">
                    <a href="logoutPage.php" class="menu__link">Logout</a>
                </li>

            </ul>
            <div class="nav-menu">
                <ul class="mobile-menu">
                    <li class="mobile-menu__item">
                        <a href="index.php" class="mobile-menu__link">Accueil</a>
                    </li>
                    <li class="mobile-menu__item">
                        <a href="history.php" class="mobile-menu__link">Historique des réservation</a>
                    </li>

                    <li class="mobile-menu__item">
                        <a href="logoutPage.php" class="mobile-menu__link">Logout</a>
                    </li>

                </ul>

            </div>

            <div class="nav__btn">
                <span class="nav__btn-line"></span>
            </div>
        </div>

    </div>

    <!--    car renting menu -->

    <section class="car-renting__wrapper user-wrapper">
        <div class="container">
            <!-- Welcome Message -->
            <section class="welcome">
                <h1 class="car-renting__text user-text ">
                    <?php
                    echo "Content de vous revoir, "?> <span style="color: var(--blue-text)"> <?php echo  htmlspecialchars($user['full_name']) . "!"; ?>
                </h1>
                <p class="welcome__text" style="color: var(--blue-text)">Que souhaiteriez-vous faire aujourd'hui ?</p>
            </section>

</header>
<main class="main">

    <div class="container">

        <div class="car-rental-menu">
            <!--    car renting form -->
            <form class="car-rental-form"  action="process-reservation.php" method="POST">

                <input type="hidden" name="user_id" value="<?php echo $user_id; ?>" />

                <!-- Départ -->
                <div class="form-group">
                    <i class="fa-solid fa-location-dot icon"></i>
                    <label for="depart" class="label">Départ</label>
                    <input type="text" class="form-control" id="depart" name="depart" placeholder="Point de départ" />
                    <span class="line"></span>
                </div>

                <!-- Destination -->
                <div class="form-group">
                    <i class="fa-solid fa-location-dot icon"></i>
                    <label for="destination" class="label">Destination</label>
                    <input type="text" id="destination" class="form-control" name="destination" placeholder="Destination"  />
                    <span class="line"></span>
                </div>

                <!-- Aujourd'hui (Date Picker) -->
                <div class="form-group">
                    <i class="fa-solid fa-calendar-days icon"></i>
                    <label for="date" class="label">Aujourd'hui</label>
                    <input type="date" id="date" class="form-control" name="date" required />
                    <span class="line"></span>
                </div>

                <!-- Passengers -->
                <div class="form-group">
                    <i class="fa-solid fa-user icon"></i>
                    <label for="passenger" class="label">Passenger</label>
                    <select id="passenger" name="passenger" class="form-control" >
                        <option value="1">1 Passenger</option>
                        <option value="2">2 Passengers</option>
                        <option value="3">3 Passengers</option>
                        <option value="4">4 Passengers</option>
                        <option value="5">5 Passengers</option>
                        <option value="6">6+ Passengers</option>
                    </select>
                    <span class="line"></span>
                </div>

                <!-- Additional Reservation Options -->
                <div class="form-group">
                    <i class="fa-solid fa-car icon"></i>
                    <label for="carType" class="label">Type de voiture</label>
                    <select id="carType" name="carType" class="form-control">
                        <option value="eco">Eco</option>
                        <option value="luxury">Luxury</option>
                        <option value="electric">Electric</option>
                    </select>
                    <span class="line"></span>
                </div>

                <!-- Reservation Time -->
                <div class="form-group">
                    <i class="fa-solid fa-clock icon"></i>
                    <label for="departure_time" class="label">Heure de départ</label>
                    <input type="time" id="departure_time" name="departure_time" class="form-control" required>
                    <span class="line"></span>
                </div>

                <!-- Search Button -->
                <div class="form-group form-group-btn">
                    <button type="submit" class="search-btn">Réserver</button>
                </div>

            </form>
        </div>

        <!-- Dashboard Actions -->
        <section class="dashboard-actions profits-wrappers profits-wrappers__user-page dashboards-wrapper">

            <div class="action-card profits-wrapper dashboard-wrapper">
                <i class="fa-solid fa-clock-rotate-left action-card__icon" ></i>
                <h3 class="profits__first-title">Afficher l'historique des réservations</h3>
                <a href="history.php" class="action-card__btn search-btn dashboard-btn" >Voir l'historique</a>
            </div>

            <div class="action-card profits-wrapper dashboard-wrapper credit-card">
                <i class="fa-solid fa-wallet action-card__icon" id="credit-icon"></i>
                <h3 class="profits__first-title" id="credit-title">Vérifier les crédits</h3>

                <div class="credit-container">
                    <!-- Credit Info Box (Initially Hidden) -->
                    <div id="credit-info" class="credit-info">
                        <p id="credit-text"></p>
                    </div>

                    <!-- Button remains in the same place -->
                    <a href="#" class="action-card__btn search-btn dashboard-btn credit-btn" id="show-credit">Voir le solde</a>
                </div>
            </div>

        </section>
        <!-- History Section -->
        <section class="history">
            <h2 class="user__title">Réservations récentes</h2>
            <div class="table-responsive">
                <table class="history-table">
                    <thead class="thead__user-page">
                    <tr>
                        <th>Date de réservation</th>
                        <th>Départ</th>
                        <th>Heure de départ</th>
                        <th>Type de voiture</th>
                        <th>Passagers</th>
                        <th>Prix du conducteur</th>
                    </tr>
                    </thead>
                    <tbody id="reservationHistory">
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $eco_friendly = $row['eco_friendly'] ? 'Eco Friendly' : 'Standard';
                            $reservation_date = htmlspecialchars($row['reservation_date']);
                            $departure_time = htmlspecialchars($row['departure_time']);
                            $passenger_count = htmlspecialchars($row['passenger']);
                            $driver_price = htmlspecialchars($row['driver_price']);
                            echo "<tr>
                            <td data-label='Date de réservation' class='history-data-label'>$reservation_date</td>
                            <td data-label='Départ' class='history-data-label'>" . htmlspecialchars($row['date']) . "</td>
                            <td data-label='Heure de départ' class='history-data-label'>$departure_time</td>
                            <td data-label='Type de voiture' class='history-data-label'>$eco_friendly</td>
                            <td data-label='Passagers' class='history-data-label'>$passenger_count</td>
                            <td data-label='Prix du conducteur' class='history-data-label'>€$driver_price</td>
                        </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>Aucune réservation trouvée.</td></tr>";
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </section>
        <!--Next ride -->
        <section class="illustration-section">
            <h2 class="illustration-title">Planifiez votre prochaine sortie</h2>
            <div class="illustration-container">
                <!-- SVG for Irregular Oval Background -->
                <svg class="svg-background" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 300" preserveAspectRatio="none">
                    <ellipse cx="200" cy="150" rx="200" ry="100" fill="rgba(0, 128, 0, 0.1)" />
                </svg>

                <!-- Car Illustration -->
                <img src="../public/assets/images/png/City%20driver-pana.png" alt="Illustration of a car and map" class="illustration-image">
            </div>
            <p class="illustration-text">Trouvez et réservez rapidement vos trajets grâce à notre plateforme de réservation transparente.</p>
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
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const creditBtn = document.getElementById("show-credit");
        const creditInfo = document.getElementById("credit-info");
        const creditText = document.getElementById("credit-text");
        const creditIcon = document.getElementById("credit-icon");
        const creditTitle = document.getElementById("credit-title");

        let isCreditVisible = false;

        function updateMargin() {
            if (window.innerWidth <= 576) {
                return ".8rem"; // Mobile view
            } else if (window.innerWidth > 576 && window.innerWidth <= 768) {
                return ".9rem";
            } else if (window.innerWidth > 768 && window.innerWidth <= 992) {
                return "3.2rem";
            } else if (window.innerWidth > 992 && window.innerWidth <= 1023) {
                return "2.8rem";
            } else {
                return "5.85rem";
            }
        }

        creditBtn.addEventListener("click", function (event) {
            event.preventDefault();

            isCreditVisible = !isCreditVisible;

            if (isCreditVisible) {
                creditText.innerHTML = `Bonjour, vous avez ${userCredits} crédits de toutes vos réservations. Si vous atteignez 300 crédits, vous pouvez obtenir une réduction de 40 % sur votre prochaine réservation.`;

                creditInfo.style.display = "block";  // Show the credit info
                creditInfo.style.padding = "0";
                creditInfo.style.marginTop = "0";
                creditIcon.style.display = "none";   // Hide the wallet icon
                creditTitle.style.display = "none";  // Hide "Vérifier les crédits"

                creditBtn.style.marginTop = updateMargin(); // Adjust margin dynamically
                creditBtn.textContent = "Retour";  // Change button text to "Retour"
            } else {
                creditInfo.style.display = "none";   // Hide the credit info
                creditIcon.style.display = "block";  // Show the wallet icon
                creditTitle.style.display = "block"; // Show "Vérifier les crédits" again

                creditBtn.textContent = "Voir le solde"; // Reset button text
                creditBtn.style.marginTop = "0"; // Reset margin
            }
        });

        // Adjust margin when resizing the window
        window.addEventListener("resize", function () {
            if (isCreditVisible) {
                creditBtn.style.marginTop = updateMargin();
            }
        });
    });
</script>

</body>
</html>
<?php
$stmt->close();
$db->close();
?>


