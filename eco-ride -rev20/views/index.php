
<?php
session_start(); // Start the session

if (isset($_SESSION['user'])): // Check if the user is logged in
    ?>
    <!-- User is logged in, show the logout link -->
    <a href="logoutPage.php" class="logout-link">Logout</a>
<?php
else:
    // If not logged in, show login/register links or redirect to login page
    ?>
    <a href="loginPage.php" class="login-link">Login</a>
    <a href="registerPage.php" class="register-link">Register</a>
<?php
endif;
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
                <a href="index.html" class="app-logo">
                    <img src="../public/assets/images/png/logo.png" alt="logo image" class="app-logo__img">
                </a>
                <a class="logo-container__text " href="index.html"> ECO RIDE </a>
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

    <!--    car renting menu -->

    <section class="car-renting__wrapper">
        <div class="container">

            <h1 class="car-renting__text">
                Adoptez <span class="big-word">EcoRide</span> : des trajets durables pour un avenir <span
                    class="big-word">éco-responsable</span> !
            </h1>
            <div class="car-rental-menu">
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
                        <label for="time" class="label">Heure de départ</label>
                        <input type="time" id="time" name="time" class="form-control" required>
                        <span class="line"></span>
                    </div>

                    <!-- Search Button -->
                    <div class="form-group form-group-btn">
                        <button type="submit" class="search-btn">Réserver</button>
                    </div>

                    <!-- User History Page -->
                    <a href="history.php" class="history-page__link">View Your Reservation History</a>
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
                <p class="ecoride-prime__text">Vous vous rendez au travail, à la salle de sport ou à l’école ?
                    Économisez encore plus en covoiturant avec BlaBlaCar Daily, notre application pour tous vos trajets
                    courts au quotidien. Profitez de nombreux avantages, dont 100 € de Prime Covoiturage.</p>
                <a href="#" class="ecoride-prime__btn">Search</a>
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
