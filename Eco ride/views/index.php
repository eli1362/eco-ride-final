
<!-- start header-->
<?php include_once 'header.php';?>
<!-- end header-->
<body>
<header class="header">

    <!--   start first navbar -->
<?php include_once 'firstNavBar.php';?>
    <!--   start first navbar -->

    <!--  start  second navbar menu -->
<?php include_once 'secondNavBar.php' ?>
    <!--   end second navbar menu -->

    <!--    car renting menu -->
<?php include_once 'carRentingMenu.php'?>
    <!--   end car renting menu -->

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
<!-- start footer -->
<?php include_once 'footer.php';?>
<!-- start footer -->