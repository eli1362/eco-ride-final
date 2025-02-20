
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
                <a href="driverInfo.php" class="menu__link">Trouver un conducteur</a>
            </li>

            <li class="menu__item">
                <a href="index.php" class="menu__link">À propos de nous</a>
            </li>
            <li class="menu__item">
                <a href="loginAdmin.php" class="menu__link">Admin Login</a>
            </li>

        </ul>
        <div class="nav-menu">
            <ul class="mobile-menu">
                <li class="mobile-menu__item">
                    <a href="driverInfo.php" class="mobile-menu__link">Trouver un conducteur</a>
                </li>


                <li class="mobile-menu__item">
                    <a href="index.php" class="mobile-menu__link">À propos de nous</a>
                </li>
                <li class="mobile-menu__item">
                    <a href="loginAdmin.php" class="mobile-menu__link">Admin Login</a>
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

