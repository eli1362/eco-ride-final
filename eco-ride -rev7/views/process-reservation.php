<?php


session_start();

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Retrieve form data
    $depart = $_POST['depart'];
    $destination = $_POST['destination'];
    $date = $_POST['date']; // Format: DD/MM/YYYY
    $time = $_POST['time']; // Format: HH:MM

    // Convert the date (DD/MM/YYYY) into YYYY-MM-DD
    $dateParts = explode('/', $date); // Splitting the DD/MM/YYYY format
    if (count($dateParts) == 3) {
        // Convert to YYYY-MM-DD
        $date = $dateParts[2] . '-' . $dateParts[1] . '-' . $dateParts[0]; // Format: YYYY-MM-DD
    }

    // Combine the date and time into a datetime string for comparison or database entry
    $selectedDateTime = $date . ' ' . $time; // Format: YYYY-MM-DD HH:MM



// Mock database of drivers
$drivers = [
    [
        'name' => 'John Doe',
        'photo' => '../public/assets/images/image/driver1.jpg',
        'rating' => 4.5,
        'remaining_seats' => 3,
        'price' => 15.00,
        'date' => '2025-01-22',
        'departure_time' => '08:00',
        'arrival_time' => '09:00',
        'eco_friendly' => true,
        'availability' => ['2025-01-22 08:00', '2025-01-22 09:30'],
    ],
    [
        'name' => 'Jane Smith',
        'photo' => '../public/assets/images/image/driver2.jpg',
        'rating' => 4.8,
        'remaining_seats' => 2,
        'price' => 20.00,
        'date' => '2025-01-22',
        'departure_time' => '09:30',
        'arrival_time' => '10:30',
        'eco_friendly' => false,
        'availability' => ['2025-01-22 09:00', '2025-01-22 11:00'],
    ],
    [
        'name' => 'Alex Green',
        'photo' => '../public/assets/images/image/driver3.jpg',
        'rating' => 5.0,
        'remaining_seats' => 4,
        'price' => 18.00,
        'date' => '2025-01-22',
        'departure_time' => '10:00',
        'arrival_time' => '11:00',
        'eco_friendly' => true,
        'availability' => ['2025-01-22 10:00', '2025-01-22 11:30'],
    ],
    [
        'name' => 'Emily White',
        'photo' => '../public/assets/images/image/driver4.jpg',
        'rating' => 4.2,
        'remaining_seats' => 3,
        'price' => 22.00,
        'date' => '2025-01-22',
        'departure_time' => '11:00',
        'arrival_time' => '12:00',
        'eco_friendly' => false,
        'availability' => ['2025-01-22 11:00', '2025-01-22 12:30'],
    ],
    [
        'name' => 'Michael Brown',
        'photo' => '../public/assets/images/image/driver5.jpg',
        'rating' => 4.7,
        'remaining_seats' => 2,
        'price' => 19.00,
        'date' => '2025-01-22',
        'departure_time' => '12:00',
        'arrival_time' => '13:00',
        'eco_friendly' => true,
        'availability' => ['2025-01-22 12:00', '2025-01-22 13:30'],
    ],
    [
        'name' => 'Sarah Taylor',
        'photo' => '../public/assets/images/image/driver6.jpg',
        'rating' => 4.3,
        'remaining_seats' => 4,
        'price' => 16.00,
        'date' => '2025-01-22',
        'departure_time' => '13:30',
        'arrival_time' => '14:30',
        'eco_friendly' => false,
        'availability' => ['2025-01-22 13:30', '2025-01-22 15:00'],
    ],
    [
        'name' => 'Chris Wilson',
        'photo' => 'public/assets/images/image/driver7.jpg',
        'rating' => 4.9,
        'remaining_seats' => 3,
        'price' => 17.50,
        'date' => '2025-01-22',
        'departure_time' => '14:00',
        'arrival_time' => '15:00',
        'eco_friendly' => true,
        'availability' => ['2025-01-22 15:00', '2025-01-22 16:30'],
    ],
    [
        'name' => 'Jessica Brown',
        'photo' => 'public/assets/images/image/driver8.jpg',
        'rating' => 4.1,
        'remaining_seats' => 5,
        'price' => 21.00,
        'date' => '2025-01-22',
        'departure_time' => '15:00',
        'arrival_time' => '16:00',
        'eco_friendly' => false,
        'availability' => ['2025-01-22 16:00', '2025-01-22 17:30'],
    ],
    [
        'name' => 'Daniel Garcia',
        'photo' => 'public/assets/images/image/driver9.jpg',
        'rating' => 4.6,
        'remaining_seats' => 2,
        'price' => 20.00,
        'date' => '2025-01-22',
        'departure_time' => '16:30',
        'arrival_time' => '17:30',
        'eco_friendly' => true,
        'availability' => ['2025-01-22 17:30', '2025-01-22 19:00'],
    ],
    [
        'name' => 'Laura Martinez',
        'photo' => 'public/assets/images/image/driver10.jpeg',
        'rating' => 4.4,
        'remaining_seats' => 3,
        'price' => 18.50,
        'date' => '2025-01-22',
        'departure_time' => '18:00',
        'arrival_time' => '19:00',
        'eco_friendly' => false,
        'availability' => ['2025-01-22 19:00', '2025-01-22 20:30'],
    ],
];
    // Find drivers who match the selected date and time
    $availableDriver = null;
    $alternativeDrivers = [];

    foreach ($drivers as $driver) {
        // Check if the driver is available at the selected time
        $driverAvailability = $driver['availability'];

        // Convert driver availability to datetime format for comparison
        foreach ($driverAvailability as $availability) {
            if ($selectedDateTime === $availability) {
                $availableDriver = $driver;
                break;
            } else if (abs(strtotime($selectedDateTime) - strtotime($availability)) <= 3600) {
                // If there's an alternative availability within an hour difference
                $alternativeDrivers[] = $driver;
            }
        }

        if ($availableDriver !== null) {
            break;
        }
    }

    // Logic to display the result to the user
    if ($availableDriver !== null) {
        // Display the selected driver's information
        $dateObj = DateTime::createFromFormat('Y-m-d', $availableDriver['date']); // Create DateTime object from Y-m-d
        $formattedDate = $dateObj->format('d-m-Y'); // Format it to DD-MM-YYYY
        echo "Driver: " . $availableDriver['name'] . "<br>";
        echo "Rating: " . $availableDriver['rating'] . "<br>";
        echo "Price: $" . $availableDriver['price'] . "<br>";
        echo "Departure Time: " . $availableDriver['departure_time'] . "<br>";
        echo "Date: " . $formattedDate . "<br>";
        echo "This is an eco-friendly ride: " . ($availableDriver['eco_friendly'] ? "Yes" : "No") . "<br>";
    } else {
        echo "No drivers available at the selected time.<br>";

        // Display alternative drivers
        if (!empty($alternativeDrivers)) {
            echo "Alternative drivers available:<br>";
            foreach ($alternativeDrivers as $driver) {
                $dateObj = DateTime::createFromFormat('Y-m-d', $driver['date']); // Create DateTime object from Y-m-d
                $formattedDate = $dateObj->format('d-m-Y'); // Format it to DD-MM-YYYY
                echo "Driver: " . $driver['name'] . "<br>";
                echo "Rating: " . $driver['rating'] . "<br>";
                echo "Price: $" . $driver['price'] . "<br>";
                echo "Departure Time: " . $driver['departure_time'] . "<br>";
                echo "Date: " . $formattedDate . "<br>";
                echo "This is an eco-friendly ride: " . ($driver['eco_friendly'] ? "Yes" : "No") . "<br>";
                echo "<br>";
            }
        } else {
            echo "Sorry, no available alternatives found.<br>";
        }
    }
}
