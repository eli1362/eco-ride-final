<?php
global $user_id;
session_start();
include_once "../config/Database.php";

// Check if user is logged in (optional: show different content for logged-in users)
$user_logged_in = isset($_SESSION['user_id']);
$error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : null;
unset($_SESSION['error_message']); // Clear the message after displaying it
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoRide - Covoiturage écologique</title>
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
<!--start menu-->
    <?php include_once "header.php"?>
<!--    finish menu-->
    <!--    car renting menu -->

    <section class="car-renting__wrapper">
        <div class="container">

            <h1 class="car-renting__text">
                Adoptez <span class="big-word">EcoRide</span> : des trajets durables pour un avenir <span
                    class="big-word">éco-responsable</span> !
            </h1>
            <!-- Display error message -->

            <div class="car-rental-menu">

                <div class="car-rental-menu">
                    <h3 class="error-message__title <?php echo $error_message ? 'show' : ''; ?>">
                        <?php echo $error_message; ?>
                    </h3>
                </div>

                <form class="car-rental-form" action="process-reservation.php" method="POST">

                    <input type="hidden" name="user_id" value="<?php echo $user_id; ?>" />

                    <!-- Départ -->
                    <div class="form-group">
                        <i class="fa-solid fa-location-dot icon"></i>
                        <label for="depart" class="label">Départ</label>
                        <input type="text" class="form-control" id="depart" name="depart" placeholder="Point de départ" />

                    </div>

                    <!-- Destination -->
                    <div class="form-group">
                        <i class="fa-solid fa-location-dot icon"></i>
                        <label for="destination" class="label">Destination</label>
                        <input type="text" id="destination" class="form-control" name="destination" placeholder="Destination"  />

                    </div>

                    <!-- Aujourd'hui (Date Picker) -->
                    <div class="form-group">
                        <i class="fa-solid fa-calendar-days icon"></i>
                        <label for="date" class="label">Aujourd'hui</label>
                        <input type="date" id="date" class="form-control" name="date">

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
            <!-- Results Section (Initially hidden) -->
            <div class="search-results" style="display:none;">
                <h2>Results:</h2>
                <div id="results-container">
                    <!-- Dynamic results will be inserted here -->
                </div>
            </div>
        </div>
        <!-- Driver details modal -->
        <div id="driver-details" class="modal">
            <div class="modal-content">
                <span id="close-btn" class="close">&times;</span>
            </div>
        </div>
    </section>
</header>
<main class="main">
    <div class="container">

        <!-- section of profits    -->

        <section class="profits-wrappers">

            <div class="profits-wrapper">

                <i class="fa-solid fa-earth-americas profits__icon"></i>
                <h3 class="profits__first-title">Conduite Impact environnemental et Économique</h3>
                <h5 class="profits__second-title">Avantages écologiques</h5>

                <ul class="profits__lists">
                    <li class="profits__list">Les éco-voitures réduisent considérablement les émissions de gaz à effet
                        de serre, favorisant un air plus pur et une planète en meilleure santé.
                    </li>
                    <li class="profits__list">Moindre dépendance aux énergies fossiles pour une transition durable.</li>
                    Moindre dépendance aux énergies fossiles pour une transition durable.
                    <li class="profits__list">Une contribution essentielle à la lutte contre le changement climatique.
                    </li>
                </ul>

            </div>
            <div class="profits-wrapper">
                <i class="fa-sharp fa-solid fa-sack-dollar profits__icon"></i>
                <h3 class="profits__first-title">Économies et innovation</h3>
                <h5 class="profits__second-title">Réduction des coûts et technologies intelligentes</h5>

                <ul class="profits__lists">
                    <li class="profits__list">Coûts d’exploitation réduits grâce à une consommation d’énergie plus
                        efficace et à des besoins de maintenance moindres.
                    </li>
                    <li class="profits__list">Économies à long terme pour les fabricants et les conducteurs.</li>
                    Moindre dépendance aux énergies fossiles pour une transition durable.
                    <li class="profits__list">Des fonctionnalités avancées telles que le freinage régénératif et une
                        conduite silencieuse pour un confort optimal.
                    </li>
                </ul>

            </div>
            <div class="profits-wrapper">
                <i class="fa-solid fa-chart-line profits__icon"></i>
                <h3 class="profits__first-title">Opportunités économiques</h3>
                <h5 class="profits__second-title">Bénéfices pour les entreprises et les usagers</h5>

                <ul class="profits__lists">
                    <li class="profits__list">Les entreprises profitent d’une demande croissante pour des solutions de
                        transport durables.
                    </li>
                    <li class="profits__list">Les subventions et incitations gouvernementales augmentent la rentabilité
                        et favorisent l’adoption.
                    </li>
                    Moindre dépendance aux énergies fossiles pour une transition durable.
                    <li class="profits__list">Soutenir les éco-voitures, c’est encourager une économie verte et stimuler
                        l’innovation.
                    </li>
                </ul>

            </div>

        </section>

        <!-- section ecoride prime    -->

        <section class="ecoride-prime__wrapper">
            <div class="ecoride-prime__photo">
                <img alt="car png" src="../public/assets/images/png/1.png" class="ecoride-prime__img">
            </div>
            <div class="ecoride-prime__description">
                <h2 class="ecoride-prime__title">Découvrez Eco Ride Daily, et recevez 100 € de Prime Covoiturage</h2>
                <p class="ecoride-prime__text">Vous allez au travail, à la salle de sport ou à l'école ? Économisez encore plus en pratiquant le covoiturage avec ecoRide, notre application dédiée à tous vos trajets quotidiens. Profitez de nombreux avantages, dont un bonus de 100€ pour le covoiturage. Connectez-vous dès maintenant et en choisissant une voiture électrique, vous pourrez profiter de réductions et économiser de l'argent. Inscrivez-vous dès aujourd'hui !</p>
                <a href="registerPage.php" class="ecoride-prime__btn">Inscrivez-vous</a>
            </div>
        </section>

        <!-- section planet    -->

        <section class="planet__wrapper">

            <div class="planet-description-image__wrapper">
                <div class="planet__description-wrapper">
                    <p class="planet__description">
                        Trouvez des locations de <span class="text">voitures économiques</span> pour voyager, économiser
                        de l'argent et, en louant une bonne voiture, protégeons ensemble notre <span class="text">planète</span>pour
                        les <span class="text">générations </span>futures.
                    </p>
                    <a href="#" class="planet__btn">Commencez votre voyage</a>
                </div>
                <div class="planet__pic">
                    <img src="../public/assets/images/image/planet.jpg" alt="planet image" class="planet__img">
                </div>
            </div>

        </section>

        <!-- section newsletter    -->

        <section class="newsletter">
            <div class="newsletter__pic">
                <img src="../public/assets/images/png/self%20driving%20car-bro.png" alt="newsletter image" class="newsletter__img">
            </div>
            <div class="newsletter-description__wrapper">
                <h2 class="ecoride-prime__title">
                    Receive our newsletter.
                </h2>
                <p class="ecoride-prime__text">
                    <span class="text-green ">Abonnez-vous</span> à notre newsletter pour recevoir des conseils de
                    voyage exclusifs, des offres privées et des alertes lorsque<br>
                    <span class="text-green text-size">les prix des locations de voitures baissent.</span>
                </p>
                <div class="email-input-wrapper">
                    <div class="email-icon">
                        <i class="fa-solid fa-envelope"></i>
                    </div>
                    <label>
                        <input type="email" class="email-input" placeholder="Enter your email address">
                    </label>
                    <button class="send-button">
                        <i class="fa-solid fa-paper-plane send-icon"></i>
                    </button>
                </div>

            </div>
        </section>
    </div>

    <!-- section application   -->

    <section class="app-promotion">
        <div class="container app-promotion__wrapper">
            <!-- Left Content -->
            <div class="app-promotion__text">
                <h2>Voyagez mieux et plus simplement avec l'application Eco Ride</h2>
                <p>
                    Tous vos trajets et billets en un seul endroit, informations à jour et
                    fonctionnalités exclusives disponibles uniquement sur mobile.
                </p>
                <div class="app-promotion__buttons">
                    <a href="#" class="app-store">
                        <img src="../public/assets/images/png/google.png" alt="Google Play Store" class="google-play"/>
                    </a>
                    <a href="#" class="app-store">
                        <img src="../public/assets/images/png/app%20store.png" alt="App Store"/>
                    </a>
                </div>
            </div>

            <!-- Right Content -->
            <div class="app-promotion__image">
                <img src="../public/assets/images/png/smartphone.png" alt="QR Code on Phone" class="app-promotion__pic"/>

            </div>
        </div>
    </section>
</main>

<footer class="footer">

<!--    start footer-->
   <?php include_once "footer.php"?>
<!--    end footer-->

</footer>





<script src="../public/assets/script/app.js"></script>
</body>
</html>
