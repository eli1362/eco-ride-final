
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
                    <a href="" class="menu__link">Dashboard</a>
                </li>
                <li class="menu__item">
                    <a href="" class="menu__link">Reservation History</a>
                </li>
                <li class="menu__item">
                    <a href="" class="menu__link">Credits</a>
                </li>
                <li class="menu__item">
                    <a href="" class="menu__link">Logout</a>
                </li>

            </ul>
            <div class="nav-menu">
                <ul class="mobile-menu">
                    <li class="mobile-menu__item">
                        <a href="" class="mobile-menu__link">Dashboard</a>
                    </li>
                    <li class="mobile-menu__item">
                        <a href="" class="mobile-menu__link">Reservation History</a>
                    </li>
                    <li class="mobile-menu__item">
                        <a href="" class="mobile-menu__link">Credits</a>
                    </li>
                    <li class="mobile-menu__item">
                        <a href="" class="mobile-menu__link">Logout</a>
                    </li>

                </ul>

            </div>

            <div class="nav__btn">
                <span class="nav__btn-line"></span>
            </div>
        </div>

    </div>

</header>
<main class="main">
    <section class="car-renting__wrapper car-renting__wrapper-car-reservation">
        <div class="container">
            <h1 class="car-renting__text car-renting__text-car-reservation">
                Adoptez <span class="big-word">EcoRide</span> : des trajets durables pour un avenir <span class="big-word">éco-responsable</span> !
            </h1>
            <div class="car-rental-menu">
                <form class="car-rental-form car-rental-form-car-reservation" action="./auth/submit-reservation.php" method="POST">
                    <div class="form-group">
                        <label for="depart" class="label">Départ</label>
                        <input type="text" class="form-control" id="depart" name="depart" placeholder="Point de départ" required />
                    </div>

                    <div class="form-group">
                        <label for="destination" class="label">Destination</label>
                        <input type="text" id="destination" class="form-control" name="destination" placeholder="Destination" required />
                    </div>

                    <div class="form-group">
                        <label for="date" class="label">Aujourd'hui</label>
                        <input type="date" id="date" class="form-control" name="date" required />
                    </div>

                    <div class="form-group">
                        <label for="passenger" class="label">Passagers</label>
                        <select id="passenger" name="passenger" class="form-control" required>
                            <option value="1">1 Passager</option>
                            <option value="2">2 Passagers</option>
                            <option value="3">3 Passagers</option>
                            <option value="4">4 Passagers</option>
                            <option value="5">5 Passagers</option>
                            <option value="6">6+ Passagers</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="carType" class="label">Type de voiture</label>
                        <select id="carType" name="carType" class="form-control" required>
                            <option value="eco">Eco</option>
                            <option value="luxury">Luxury</option>
                            <option value="electric">Electric</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="time" class="label">Heure de départ</label>
                        <input type="time" id="time" name="time" class="form-control" required />
                    </div>

                    <div class="form-group form-group-btn">
                        <button type="submit" class="search-btn">Réserver</button>
                    </div>
                </form>
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


