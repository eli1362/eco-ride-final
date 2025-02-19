<?php
session_start();

$type = $_GET['type'] ?? null;

if ($type === 'success') {
    // Make sure the driver data is in the session
    if (isset($_SESSION['available_driver'])) {
        $availableDriver = $_SESSION['available_driver'];

        echo "<h1>Reservation Confirmed</h1>";
        echo "<img src='{$availableDriver['photo']}' alt='{$availableDriver['name']}' style='width: 150px; height: auto;'>";
        echo "<p>Driver: {$availableDriver['name']}</p>";
        echo "<p>Rating: {$availableDriver['rating']}</p>";
        echo "<p>Price: \${$availableDriver['price']}</p>";
        echo "<p>Departure Time: {$availableDriver['availability'][0]}</p>";
        echo $availableDriver['eco_friendly'] ? "<p>This is an eco-friendly ride!</p>" : "";
    } else {
        echo "<p>No driver found</p>";
    }
} elseif ($type === 'alternatives') {
    if (isset($_SESSION['alternative_drivers']) && !empty($_SESSION['alternative_drivers'])) {
        $alternativeDrivers = $_SESSION['alternative_drivers'];

        echo "<h1>No Exact Match Found</h1>";
        echo "<p>Here are some alternative drivers:</p>";
        foreach ($alternativeDrivers as $driver) {
            echo "<div style='border: 1px solid #ddd; margin: 10px; padding: 10px;'>";
            echo "<img src='{$driver['photo']}' alt='{$driver['name']}' style='width: 150px; height: auto;'>";
            echo "<p>Driver: {$driver['name']}</p>";
            echo "<p>Rating: {$driver['rating']}</p>";
            echo "<p>Price: \${$driver['price']}</p>";
            echo "<p>Next Available Time: {$driver['availability'][0]}</p>";
            echo $driver['eco_friendly'] ? "<p><strong>Eco-Friendly Option!</strong></p>" : "";
            echo "</div>";
        }
    } else {
        echo "<p>No alternative drivers found</p>";
    }
} elseif ($type === 'no-alternatives') {
    $message = $_SESSION['message'] ?? "No drivers are available.";
    echo "<h1>No Alternatives Found</h1>";
    echo "<p>$message</p>";
    echo "<p>Consider trying a different date or time.</p>";
} else {
    echo "<h1>Error: Invalid Request</h1>";
}

