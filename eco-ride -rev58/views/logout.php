<?php
// Start the session
session_start();

// Destroy the session
session_unset();  // Removes all session variables
session_destroy();  // Destroys the session

// Redirect to the admin login page
header("Location: loginAdmin.php");
exit();
?>

