<?php
// Start the session
session_start();

// Include database connection
include 'db_connect.php';

// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if the user is not logged in
    header("Location: login.php");
    exit();
}

// Fetch and store user details in session if not already set
if (!isset($_SESSION['username'])) {
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT username FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['username'] = $user['username'];
    } else {
        // If user not found, destroy session and redirect
        session_destroy();
        header("Location: login.php");
        exit();
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="index.css">
  <title>Page Title</title>
 
</head>
<body>
<header>
  <!-- User Info Section -->

   <div class="logo-container">
      <img src="logo.png" alt="Website Logo">
   </div>

</header>
