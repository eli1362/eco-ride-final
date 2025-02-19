<?php
// Include your database connection file
global $db, $availableDriver;
include("../config/Database.php");

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ensure form data exists before using them
    $depart = isset($_POST['depart']) ? $_POST['depart'] : '';
    $destination = isset($_POST['destination']) ? $_POST['destination'] : '';
    $date = isset($_POST['date']) ? $_POST['date'] : '';
    $time = isset($_POST['time']) ? $_POST['time'] : '';
    $driver_id = isset($_POST['driver_id']) ? $_POST['driver_id'] : null;
    $driver_name = isset($_POST['driver_name']) ? $_POST['driver_name'] : '';
    $driver_price = isset($_POST['driver_price']) ? $_POST['driver_price'] : '';
    $eco_friendly = isset($_POST['eco_friendly']) ? $_POST['eco_friendly'] : '';

    // Check if the necessary values are set
    if (!$driver_id || !$driver_name || !$driver_price) {
        echo "Error: Missing driver details.";
        exit();
    }

    // Assuming user is logged in and you have the user_id in session
    session_start(); // Make sure session is started
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

    if (!$user_id) {
        echo "Error: User is not logged in.";
        exit();
    }

    // Check if the driver exists in the database
    $query = "SELECT driver_id FROM drivers WHERE driver_id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("i", $driver_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        // If the driver doesn't exist, show an error
        echo "Error: Driver ID does not exist in the database.";
        exit();
    }

    // Prepare and execute the SQL statement to insert the reservation
    $query = "INSERT INTO reservations (user_id, driver_id, driver_name, driver_price, departure_time, date, eco_friendly) 
              VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $db->prepare($query);
    $stmt->bind_param("iisssss", $user_id, $driver_id, $driver_name, $driver_price, $time, $date, $eco_friendly);

    if ($stmt->execute()) {
        // Get the reservation ID after insert
        $reservation_id = $db->insert_id;

        // Redirect to the confirmation page with the reservation ID
        header("Location: confirmation.php?reservation_id=" . $reservation_id);
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
}
?>

<!-- Reservation Form -->
<form class="car-rental-form" method="POST" action="reservation.php">
    <div class="form-group">
        <i class="fa-solid fa-location-dot icon"></i>
        <label for="depart" class="label">Départ</label>
        <input type="text" name="depart" class="form-control" id="depart" value="<?php echo htmlspecialchars($depart); ?>" placeholder="Departure">
        <span class="line"></span>
    </div>

    <div class="form-group">
        <i class="fa-solid fa-location-dot icon"></i>
        <label for="destination" class="label">Destination</label>
        <input type="text" name="destination" id="destination" class="form-control" value="<?php echo htmlspecialchars($destination); ?>" placeholder="Destination">
        <span class="line"></span>
    </div>

    <div class="form-group">
        <i class="fa-solid fa-calendar-days icon"></i>
        <label for="date" class="label">Aujourd'hui</label>
        <input type="date" name="date" id="date" class="form-control" value="<?php echo htmlspecialchars($date); ?>">
        <span class="line"></span>
    </div>

    <div class="form-group">
        <i class="fa-solid fa-clock icon"></i>
        <label for="time" class="label">Heure de départ</label>
        <input type="time" name="time" class="form-control" id="time" value="<?php echo htmlspecialchars($time); ?>">
        <span class="line"></span>
    </div>

    <div class="form-group form-group-btn">
        <button type="submit" class="search-btn">Submit</button>
    </div>
</form>

<!-- Display the driver result -->
<?php if ($availableDriver !== null): ?>
    <div class="driver-info">
        <img src="<?php echo $availableDriver['photo']; ?>" alt="Driver Photo" class="driver-photo">
        <div class="driver-details">
            <h2>" <?php echo $availableDriver['name']; ?> "</h2>
            <p class="driverinfo-title">Rating: <span class="driverInfo-span"> <?php echo $availableDriver['rating']; ?> </span> ⭐</p>
            <p class="driverinfo-title">Price: <span class="driverInfo-span"> $<?php echo $availableDriver['price']; ?></span></p>
            <p class="driverinfo-title">Departure Time: <span class="driverInfo-span"> <?php echo $availableDriver['departure_time']; ?></span></p>
            <p class="driverinfo-title">Date: <span class="driverInfo-span"> <?php echo $availableDriver['date']; ?></span></p>
            <p class="driverinfo-title">Eco-Friendly Ride: <span class="driverInfo-span"> <?php echo ($availableDriver['eco_friendly'] ? "Yes" : "No"); ?></span></p>

            <!-- Reservation Form -->
            <form action="reservation.php" method="POST">
                <input type="hidden" name="driver_id" value="<?php echo $availableDriver['driver_id']; ?>">
                <input type="hidden" name="driver_name" value="<?php echo $availableDriver['name']; ?>">
                <input type="hidden" name="driver_price" value="<?php echo $availableDriver['price']; ?>">
                <input type="hidden" name="driver_departure_time" value="<?php echo $availableDriver['departure_time']; ?>">
                <input type="hidden" name="driver_date" value="<?php echo $availableDriver['date']; ?>">
                <input type="hidden" name="driver_eco_friendly" value="<?php echo $availableDriver['eco_friendly']; ?>">
                <button type="submit" class="search-btn search-btn__driverInfo" id="driverReserve-button">Reserve</button>
            </form>
        </div>
    </div>
<?php else: ?>
    <p class="profits__first-title profits__first-title__driverInfo">No drivers available at the selected time.</p>
    <?php if (!empty($alternativeDrivers)): ?>
        <p class="driver-alternative">Alternative drivers available:</p>
        <?php foreach ($alternativeDrivers as $driver): ?>
            <div class="driver-info">
                <img src="<?php echo $driver['photo']; ?>" alt="Driver Photo" class="driver-photo">
                <div class="driver-details">
                    <h2>" <?php echo $driver['name']; ?> "</h2>
                    <p class="driverinfo-title">Rating: <span class="driverInfo-span"><?php echo $driver['rating']; ?></span></p>
                    <p class="driverinfo-title">Price: <span class="driverInfo-span">$<?php echo $driver['price']; ?></span> ⭐</p>
                    <p class="driverinfo-title">Departure Time: <span class="driverInfo-span"> <?php echo $driver['departure_time']; ?></span></p>
                    <p class="driverinfo-title">Date: <span class="driverInfo-span"> <?php echo $driver['date']; ?></span></p>
                    <p class="driverinfo-title">Eco-Friendly Ride: <span class="driverInfo-span"> <?php echo ($driver['eco_friendly'] ? "Yes" : "No"); ?></span></p>

                    <!-- Reservation Form -->
                    <form action="reservation.php" method="POST">
                        <input type="hidden" name="driver_id" value="<?php echo $driver['driver_id']; ?>">
                        <input type="hidden" name="driver_name" value="<?php echo $driver['name']; ?>">
                        <input type="hidden" name="driver_price" value="<?php echo $driver['price']; ?>">
                        <input type="hidden" name="driver_departure_time" value="<?php echo $driver['departure_time']; ?>">
                        <input type="hidden" name="driver_date" value="<?php echo $driver['date']; ?>">
                        <input type="hidden" name="driver_eco_friendly" value="<?php echo $driver['eco_friendly']; ?>">
                        <button type="submit" class="search-btn search-btn__driverInfo" id="driverReserve-button">Reserve</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Sorry, no available alternatives found.</p>
    <?php endif; ?>
<?php endif; ?>
