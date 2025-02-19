<?php
include '../config/Database.php';

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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background: linear-gradient(to bottom, #C8D5B9, #FAFAF0);
            font-family: Arial, sans-serif;
            padding: 20px;
            height: 100vh;
        }

        h1, h2, h3 {
            text-align: center;
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

        h2, h3 {
            color: #333;
        }
    </style>
</head>
<body>
<h1>Admin Dashboard</h1>

<form action="" method="get">
    <label for="start_date">Start Date:</label>
    <input type="date" name="start_date" value="<?= htmlspecialchars($startDate) ?>" required>
    <label for="end_date">End Date:</label>
    <input type="date" name="end_date" value="<?= htmlspecialchars($endDate) ?>" required>

    <!-- Filter Button with same style -->
    <div class="btn-container">
        <button type="submit">Filter</button>
        <button type="button" onclick="downloadCSV()">Download CSV</button>
    </div>
</form>

<h2>Total Earnings: $<?= number_format($totalEarnings, 2) ?> | Total Reservations: <?= $totalReservations ?></h2>
<h3>Total Eco-Friendly Cars: <?= $totalEcoFriendly ?> | Total Non-Eco-Friendly Cars: <?= $totalNonEcoFriendly ?></h3>

<canvas id="earningsAndReservationsChart"></canvas>
<canvas id="ecoFriendlyChart"></canvas>

<script>
    // Earnings and Reservations Chart
    const ctx1 = document.getElementById('earningsAndReservationsChart').getContext('2d');
    const earningsAndReservationsData = <?= json_encode($chartData) ?>;

    new Chart(ctx1, {
        type: 'bar',
        data: {
            labels: earningsAndReservationsData.map(data => data.date),
            datasets: [
                {
                    label: 'Earnings Per Day',
                    data: earningsAndReservationsData.map(data => data.earnings),
                    backgroundColor: 'green'
                },
                {
                    label: 'Reservations Per Day',
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

    // Eco-Friendly vs Non-Eco-Friendly Chart
    const ctxEcoFriendly = document.getElementById('ecoFriendlyChart').getContext('2d');
    new Chart(ctxEcoFriendly, {
        type: 'bar',
        data: {
            labels: earningsAndReservationsData.map(data => data.date),
            datasets: [
                {
                    label: 'Eco-Friendly Cars',
                    data: earningsAndReservationsData.map(data => data.eco_friendly),
                    backgroundColor: 'green'
                },
                {
                    label: 'Non-Eco-Friendly Cars',
                    data: earningsAndReservationsData.map(data => data.non_eco_friendly),
                    backgroundColor: 'red'
                }
            ]
        }
    });

    // CSV download function
    function downloadCSV() {
        const csvData = [
            ['Date', 'Total Earnings', 'Reservations', 'Eco-Friendly Cars', 'Non-Eco-Friendly Cars']
        ];

        earningsAndReservationsData.forEach(data => {
            csvData.push([data.date, data.earnings, data.reservations, data.eco_friendly, data.non_eco_friendly]);
        });

        let csvContent = 'data:text/csv;charset=utf-8,' + csvData.map(row => row.join(',')).join('\n');
        let encodedUri = encodeURI(csvContent);
        let link = document.createElement('a');
        link.setAttribute('href', encodedUri);
        link.setAttribute('download', 'admin_dashboard_data.csv');
        link.click();
    }
</script>
</body>
</html>
