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

if (!isset($user)) {
    echo "<h4 style='color: red;font-family:Montserrat-semiBold,serif'>User data is not available.</h4>";
    exit();
}

// Fetch reservation data for the logged-in user
$sql = "SELECT reservation_id, driver_id, driver_price, departure_time, date, eco_friendly, passenger, reservation_date 
        FROM reservations WHERE user_id = ? ORDER BY reservation_date DESC LIMIT 1";
$stmt = $db->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $reservation = $result->fetch_assoc();

} else {
    echo "<h4>No reservation found for this user.</h4>";
}

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
echo "<script>var fullName = '" . htmlspecialchars($user['full_name']) . "';</script>";

// Fetch reservation history for the logged-in user
$sql = "SELECT * FROM reservations WHERE user_id = ? ORDER BY reservation_date DESC limit 1";
$stmt = $db->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// choosing the role


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

    <!--   choose the role -->
    <script>
        function toggleFields() {
            var role = document.getElementById('role_id').value;
            document.getElementById('chauffeurFields').style.display = (role_id === 'chauffeur' || role_id === 'both') ? 'block' : 'none';
        }

        <!--   choose the photo -->

            function previewImage(event) {
            var reader = new FileReader();
            reader.onload = function() {
            var output = document.getElementById('photoPreview');
            output.src = reader.result;
            output.style.display = 'block'; // Display the image after it's loaded
        };
            reader.readAsDataURL(event.target.files[0]);
        }

    </script>

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
                    <a href="index.php" class="menu__link">Accueil</a>
                </li>
                <li class="menu__item">
                    <a href="history.php" class="menu__link">Historique des réservation</a>
                </li>
                <li class="menu__item">
                    <a href="history-carpool.php" class="menu__link">Historique de carpooling</a>
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
                        <a href="history-carpool.php" class="mobile-menu__link">Historique des carpooling</a>
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
                    echo "Content de vous revoir, " ?> <span
                            style="color: var(--blue-text)"> <?php echo htmlspecialchars($user['full_name']) . "!"; ?>
                </h1>
                <p class="welcome__text" style="color: var(--blue-text)">Que souhaiteriez-vous faire aujourd'hui ?</p>
            </section>

</header>
<main class="main">

    <div class="container">
        <div class="car-rental-menu">
            <!-- Car renting form updated to include the rating section -->
            <form class="car-rental-form" action="process-reservation.php" method="POST">

                <input type="hidden" name="user_id" value="<?php echo $user_id; ?>"/>

                <!-- Départ -->
                <div class="form-group">
                    <i class="fa-solid fa-location-dot icon"></i>
                    <label for="depart" class="label">Départ</label>
                    <input type="text" class="form-control" id="depart" name="depart" placeholder="Point de départ"/>

                </div>

                <!-- Destination -->
                <div class="form-group">
                    <i class="fa-solid fa-location-dot icon"></i>
                    <label for="destination" class="label">Destination</label>
                    <input type="text" id="destination" class="form-control" name="destination"
                           placeholder="Destination"/>

                </div>

                <!-- Aujourd'hui (Date Picker) -->
                <div class="form-group">
                    <i class="fa-solid fa-calendar-days icon"></i>
                    <label for="date" class="label">Aujourd'hui</label>
                    <input type="date" id="date" class="form-control" name="date" required/>

                </div>

                <!-- Rating (Updated Section) -->
                <div class="form-group">
                    <i class="fa-solid fa-star icon"></i>
                    <label for="rating" class="label">Rating</label>
                    <select id="rating" name="rating" class="form-control">
                        <option value="1">1 star</option>
                        <option value="2">2 stars</option>
                        <option value="3">3 stars</option>
                        <option value="4">4 stars</option>
                        <option value="5">5 stars</option>
                    </select>

                </div>

                <!-- Passengers -->
                <div class="form-group">
                    <i class="fa-solid fa-user icon"></i>
                    <label for="passenger" class="label">Passenger</label>
                    <select id="passenger" name="passenger" class="form-control">
                        <option value="1">1 Passenger</option>
                        <option value="2">2 Passengers</option>
                        <option value="3">3 Passengers</option>
                        <option value="4">4 Passengers</option>
                    </select>

                </div>

                <!-- Additional Reservation Options -->
                <div class="form-group">
                    <i class="fa-solid fa-car icon"></i>
                    <label for="carType" class="label">Type de voiture</label>
                    <select id="carType" name="carType" class="form-control">
                        <option value="electric">Electric</option>
                        <option value="non-electric">Non-Electric</option>
                    </select>

                </div>

                <!-- Reservation Time -->
                <div class="form-group">
                    <i class="fa-solid fa-clock icon"></i>
                    <label for="departure_time" class="label">Heure de départ</label>
                    <input type="time" id="departure_time" name="departure_time" class="form-control" required>

                </div>

                <!-- Search Button -->
                <div class="form-group form-group-btn">
                    <button type="submit" class="search-btn">Réserver</button>
                </div>

            </form>
        </div>

        <!-- Choosing the Roles -->

        <form class="car-rental-form form-roles" method="post" action="save_driver.php" enctype="multipart/form-data">

            <div class="form-group">
                <label class="label" for="role_id">Choisissez votre rôle :</label>
                <select class="form-control role-control" name="role_id" id="role_id" onchange="toggleFields()">
                    <option value="1">Passager</option>
                    <option value="2">Chauffeur</option>
                    <option value="3">Passager & Chauffeur</option>
                </select>
            </div>

            <div class="form-group">
                <label class="label" for="phone">Téléphone :</label>
                <input class="form-control" type="tel" name="phone" id="phone" required pattern="[0-9]{10}" placeholder="Entrez votre numéro">
            </div>

            <div id="chauffeurFields" style="display:none;">
                <h3 class="profits__first-title">Informations du véhicule</h3>

                <div class="form-group form-group-roles">
                    <label class="label" for="name">Name :</label>
                    <input class="form-control form-control-roles" type="text" name="name" required><br>
                </div>

                <div class="form-group form-group-roles">
                    <label class="label" for="photo">Photo :</label>
                    <input class="form-control form-control-roles" type="file" name="photo" id="photo" required onchange="previewImage(event)">
                    <br>
                    <img id="photoPreview" src="" alt="driver-photo" style="display: none; width: 150px; height: 150px; object-fit: cover;">
                </div>

                <div class="form-group form-group-roles">
                    <label class="label" for="price">Price :</label>
                    <input class="form-control form-control-roles" type="text" name="price" required><br>
                </div>

                <div class="form-group form-group-roles">
                    <label class="label" for="date">Date :</label>
                    <input class="form-control form-control-roles" type="datetime-local" id="date" name="date">
                </div>

                <div class="form-group form-group-roles">
                    <label class="label" for="departure_time">Départure-time :</label>
                    <input class="form-control form-control-roles" type="time" name="departure_time" required><br>
                </div>

                <div class="form-group form-group-roles">
                    <label class="label" for="arrival_time">Arrival_time :</label>
                    <input class="form-control form-control-roles" type="time" name="arrival_time" required><br>
                </div>

                <div class="form-group form-group-roles">
                    <label for="carType" class="label">Type de voiture</label>
                    <select id="carType" name="carType" class="form-control form-control-roles">
                        <option value="electric">Electric</option>
                        <option value="non-electric">Non-Electric</option>
                    </select>
                </div>

                <div class="form-group form-group-roles">
                    <label class="label" for="plate_number">Plaque d'immatriculation :</label>
                    <input class="form-control form-control-roles" type="text" name="plate_number" id="plate_number" required><br>
                </div>

                <div class="form-group form-group-roles">
                    <label class="label" for="registration_date">Date d'inscription :</label>
                    <input class="form-control form-control-roles" type="date" name="registration_date" required><br>
                </div>

                <div class="form-group form-group-roles">
                    <label class="label" for="model">Modèle :</label>
                    <input class="form-control form-control-roles" type="text" name="model" id="model" required><br>
                </div>

                <div class="form-group form-group-roles">
                    <label class="label" for="color">Couleur :</label>
                    <input class="form-control form-control-roles" type="text" name="color" id="color" required><br>
                </div>

                <div class="form-group form-group-roles">
                    <label class="label" for="brand">Marque :</label>
                    <input class="form-control form-control-roles" type="text" name="brand" required><br>
                </div>

                <div class="form-group form-group-roles">
                    <label class="label" for="remaining_seats">Nombre de places disponibles :</label>
                    <input class="form-control form-control-roles" type="number" name="remaining_seats" required><br>
                </div>

                <div class="form-group form-group-roles form-group-preference">
                    <h3 class="profits__first-title">Préférences</h3>
                    <div class="form-group">
                        <label class="label" for="smoker">Fumeur :</label>
                        <select name="smoker" class="form-control form-control-roles">
                            <option value="1">Oui</option>
                            <option value="0">Non</option>
                        </select><br>
                    </div>
                </div>

                <div class="form-group form-group-roles">
                    <label class="label" for="animals">Animaux :</label>
                    <select name="animals" class="form-control form-control-roles">
                        <option value="1">Accepté</option>
                        <option value="0">Non accepté</option>
                    </select><br>
                </div>

                <div class="form-group form-group-roles">
                    <label class="label" for="custom_preferences">Autres préférences :</label>
                    <textarea name="custom_preferences" class="preference-textarea"></textarea><br>
                </div>
            </div>

            <button type="submit" class="search-btn">Enregistrer</button>

            <?php
            if (isset($_SESSION['success_message'])) {
                echo "<h3 style='color: green; font-weight: bold; text-align: center'>" . $_SESSION['success_message'] . "</h3>";
                unset($_SESSION['success_message']);
            }
            if (isset($_SESSION['error_message'])) {
                echo "<h3 style='color: red; font-weight: bold;text-align: center'>" . $_SESSION['error_message'] . "</h3>";
                unset($_SESSION['error_message']);
            }
            ?>

        </form>



        <!-- Dashboard Actions -->
        <section class="dashboard-actions profits-wrappers profits-wrappers__user-page dashboards-wrapper">

            <div class="action-card profits-wrapper dashboard-wrapper">
                <i class="fa-solid fa-clock-rotate-left action-card__icon"></i>
                <h3 class="profits__first-title">Afficher l'historique des réservations</h3>
                <a href="history.php" class="action-card__btn search-btn dashboard-btn">Voir l'historique</a>
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
                    <a href="#" class="action-card__btn search-btn dashboard-btn credit-btn" id="show-credit">Voir le
                        solde</a>
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
                        echo "<tr><td  colspan='6' style='color: red;font-family: Montserrat-semiBold,serif;font-size: 1.6rem;justify-content: center;margin-top: 3rem'>Aucune réservation trouvée.</td></tr>";
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
                <svg class="svg-background" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 300"
                     preserveAspectRatio="none">
                    <ellipse cx="200" cy="150" rx="200" ry="100" fill="rgba(0, 128, 0, 0.1)"/>
                </svg>

                <!-- Car Illustration -->
                <img src="../public/assets/images/png/City%20driver-pana.png" alt="Illustration of a car and map"
                     class="illustration-image">
            </div>
            <p class="illustration-text">Trouvez et réservez rapidement vos trajets grâce à notre plateforme de
                réservation transparente.</p>
        </section>
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
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const creditBtn = document.getElementById("show-credit");
        const creditInfo = document.getElementById("credit-info");
        const creditText = document.getElementById("credit-text");
        const creditIcon = document.getElementById("credit-icon");
        const creditTitle = document.getElementById("credit-title");

        let isCreditVisible = false;

        // Function to update margin based on window width
        function updateMargin() {
            if (window.innerWidth <= 576) {
                return ".8rem"; // Mobile view
            } else if (window.innerWidth > 576 && window.innerWidth <= 768) {
                return ".9rem"; // Tablet view
            } else if (window.innerWidth > 768 && window.innerWidth <= 992) {
                return "3.2rem"; // Small desktop view
            } else if (window.innerWidth > 992 && window.innerWidth <= 1023) {
                return "2.8rem"; // Medium desktop view
            } else {
                return "5.85rem"; // Large desktop view
            }
        }

        // Show/hide credit information when button is clicked
        creditBtn.addEventListener("click", function (event) {
            event.preventDefault();

            isCreditVisible = !isCreditVisible;

            if (isCreditVisible) {
                creditText.innerHTML = `Bonjour, <span style="color: red; font-weight: bold;">${fullName}</span>, vous avez <span style="color: red; font-weight: bold;">${userCredits} crédits</span>
        de toutes vos réservations. Si vous atteignez <span style="color: red; font-weight: bold;">300 crédits</span>,
        vous pouvez obtenir une <span style="color: red; font-weight: bold;">réduction de 40 %</span> sur votre prochaine réservation.`;

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


