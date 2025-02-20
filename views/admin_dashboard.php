<?php
global $db;
session_start();
include '../config/Database.php';

// Vérifier si l'administrateur est connecté
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: loginAdmin.php');
    exit();
}

$startDate = date('Y-m-d', strtotime('-7 days'));
$endDate = date('Y-m-d');
$totalEarnings = 0;
$totalReservations = 0;
$totalEcoFriendly = 0;
$totalNonEcoFriendly = 0;
$chartData = [];

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['start_date']) && isset($_GET['end_date'])) {
        $startDate = $_GET['start_date'];
        $endDate = $_GET['end_date'];

        if (DateTime::createFromFormat('Y-m-d', $startDate) && DateTime::createFromFormat('Y-m-d', $endDate)) {
            $sql = "
                SELECT r.reservation_date, 
                       COUNT(r.reservation_id) AS total_reservations, 
                       SUM(c.price) AS total_earnings, 
                       SUM(CASE WHEN c.eco_friendly = 1 THEN 1 ELSE 0 END) AS eco_friendly_count, 
                       SUM(CASE WHEN c.eco_friendly = 0 THEN 1 ELSE 0 END) AS non_eco_friendly_count
                FROM reservations r
                JOIN carpool c ON r.user_id = c.user_id AND r.driver_id = c.driver_id
                WHERE DATE(r.reservation_date) BETWEEN ? AND ?
                AND c.status = 'finished'
                GROUP BY DATE(r.reservation_date)
                ORDER BY r.reservation_date DESC
            ";

            if ($stmt = $db->prepare($sql)) {
                $stmt->bind_param("ss", $startDate, $endDate);
                $stmt->execute();
                $result = $stmt->get_result();

                while ($row = $result->fetch_assoc()) {
                    $chartData[] = [
                        'date' => $row['reservation_date'],
                        'earnings' => $row['total_earnings'],
                        'reservations' => $row['total_reservations'],
                        'eco_friendly' => $row['eco_friendly_count'],
                        'non_eco_friendly' => $row['non_eco_friendly_count']
                    ];
                    $totalEarnings += $row['total_earnings'];
                    $totalReservations += $row['total_reservations'];
                    $totalEcoFriendly += $row['eco_friendly_count'];
                    $totalNonEcoFriendly += $row['non_eco_friendly_count'];
                }
                $stmt->close();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord Admin</title>
    <link rel="icon" type="image/png" href="../public/assets/images/png/circle%20(1).png">
    <link rel="stylesheet" href="../public/assets/styles/reset.css">
    <link rel="stylesheet" href="../public/assets/styles/fonts.css">
    <link rel="stylesheet" href="../public/assets/styles/grid.css">
    <link rel="stylesheet" href="../public/assets/styles/app.css">
    <link rel="stylesheet" href="../public/assets/styles/responsive.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>


        h1, h2, h3 {
            text-align: center;
        }

        .navbar {
            display: flex;
            justify-content: center;
            background-color: #4CAF50;
            padding: 15px;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            font-size: 18px;
            margin: 0 15px;
            padding: 10px;
        }

        .navbar a:hover {
            background-color: #45a049;
            border-radius: 5px;
        }

        form {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px;
        }

        form label {
            margin-right: 10px;
        }

        form input {
            margin-right: 10px;
        }

        .btn-container {
            display: flex;
            align-items: center;
            margin-left: 20px;
        }

        .btn-container button {
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            margin-left: 10px;
        }

        .btn-container button:hover {
            background-color: #45a049;
        }

        canvas {
            display: block;
            margin: 3rem auto;
            max-width: 100%;

        }
    </style>
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


<h1 class="login__title">Tableau de Bord Admin</h1>

<form action="" method="get">
    <label for="start_date">Date de Début :</label>
    <input type="date" name="start_date" value="<?= htmlspecialchars($startDate) ?>" required>
    <label for="end_date">Date de Fin :</label>
    <input type="date" name="end_date" value="<?= htmlspecialchars($endDate) ?>" required>

    <div class="btn-container">
        <button type="submit">Filtrer</button>
        <button type="button" onclick="downloadCSV()">Télécharger CSV</button>
    </div>
</form>

<h2>Gains Totaux : $<?= number_format($totalEarnings, 2) ?> | Réservations Totales : <?= $totalReservations ?></h2>
<h3>Voitures Éco-Responsables : <?= $totalEcoFriendly ?> | Non Éco-Responsables : <?= $totalNonEcoFriendly ?></h3>

<canvas id="earningsAndReservationsChart"></canvas>
<canvas id="ecoFriendlyChart"></canvas>

<script>
    const earningsAndReservationsData = <?= json_encode($chartData) ?>;

    new Chart(document.getElementById('earningsAndReservationsChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: earningsAndReservationsData.map(data => data.date),
            datasets: [
                {
                    label: 'Gains par Jour',
                    data: earningsAndReservationsData.map(data => data.earnings),
                    backgroundColor: 'green'
                },
                {
                    label: 'Réservations par Jour',
                    data: earningsAndReservationsData.map(data => data.reservations),
                    backgroundColor: 'blue'
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toFixed(2);
                        }
                    }
                }
            }
        }
    });

    new Chart(document.getElementById('ecoFriendlyChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: earningsAndReservationsData.map(data => data.date),
            datasets: [
                {
                    label: 'Voitures Éco',
                    data: earningsAndReservationsData.map(data => data.eco_friendly),
                    backgroundColor: 'green'
                },
                {
                    label: 'Non Éco',
                    data: earningsAndReservationsData.map(data => data.non_eco_friendly),
                    backgroundColor: 'red'
                }
            ]
        }
    });

    function downloadCSV() {
        let csvContent = 'data:text/csv;charset=utf-8,Date,Gains,Réservations,Éco,Non Éco\n' +
            earningsAndReservationsData.map(data => `${data.date},${data.earnings},${data.reservations},${data.eco_friendly},${data.non_eco_friendly}`).join('\n');

        let link = document.createElement('a');
        link.setAttribute('href', encodeURI(csvContent));
        link.setAttribute('download', 'dashboard_data.csv');
        link.click();
    }
</script>

</body>
</html>
