<?php
// Include your database connection file
global $db, $availableDriver;
include("../config/Database.php");

$user_id = $_POST['user_id'];
$driver_id = $_POST['driver_id'];
$passenger = $_POST['passenger'];

// Step 3: Fetch Driver Details
$query = "SELECT * FROM drivers WHERE driver_id = :driver_id";
$stmt = $db->prepare($query);
$stmt->execute([':driver_id' => $driver_id]);
$driver = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$driver) {
    die("Driver not found.");
}

// Step 4: Check Remaining Seats
if ($driver['remaining_seats'] < $passenger) {
    die("Not enough seats available.");
}

// Step 5: Update Remaining Seats
$newRemainingSeats = $driver['remaining_seats'] - $passenger;
$updateQuery = "UPDATE drivers SET remaining_seats = :remaining_seats WHERE driver_id = :driver_id";
$stmt = $db->prepare($updateQuery);
$stmt->execute([
    ':remaining_seats' => $newRemainingSeats,
    ':driver_id' => $driver_id
]);

// Step 6: Add Reservation to Database
$reservationQuery = "
    INSERT INTO reservations (user_id, driver_id, driver_name, driver_price, departure_time, date, eco_friendly, passenger) 
    VALUES (:user_id, :driver_id, :driver_name, :driver_price, :departure_time, :date, :eco_friendly, :passenger)
";
$stmt = $db->prepare($reservationQuery);
$stmt->execute([
    ':user_id' => $user_id,
    ':driver_id' => $driver_id,
    ':driver_name' => $driver['name'],
    ':driver_price' => $driver['price'],
    ':departure_time' => $driver['departure_time'],
    ':date' => $driver['date'],
    ':eco_friendly' => $driver['eco_friendly'],
    ':passenger' => $passenger
]);

// Step 7: Update User Credits
$credits = $driver['eco_friendly'] ? 5 : 2;
$updateCreditsQuery = "UPDATE users SET credits = credits + :credits WHERE id = :user_id";
$stmt = $db->prepare($updateCreditsQuery);
$stmt->execute([
    ':credits' => $credits,
    ':user_id' => $user_id
]);

echo "Reservation successful!";

