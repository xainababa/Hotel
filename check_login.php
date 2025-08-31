<?php 
session_start();

// Check if user logged in by checking essential session vars
if (isset($_SESSION["email"]) && isset($_SESSION["id"])) {
    // User logged in, allow access
} else {
    // Not logged in, redirect to login page
    header("Location: login.php");
    exit();
}
?>
