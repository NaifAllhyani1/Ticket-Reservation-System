<!DOCTYPE html>
<html lang="en">
<head>
<?php
session_start();

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    // Include database connection
    require_once 'db_connect.php';

    // Fetch the username from the database using the user ID
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT username FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['username'] = $user['username']; // Store the username in the session
    } else {
        // If the user ID is invalid, log them out
        session_destroy();
        header("Location: login.php");
        exit();
    }
} else {
    // If the user is not logged in, redirect to the login page
    header("Location: login.php");
    exit();
}

// If logout is requested, destroy the session
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}
?>

  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ticket Reservation System - Home Page</title>
 <link rel="stylesheet" href="index.css">
</head>
<body>
  <header>
    <div class="logo-container">
      <img src="logo.png" alt="Website Logo">
    </div>

    <!-- User Info Section -->
    <div class="action">
   <div class="user-info" onclick="menuToggle();">
      <img src="user3.png" alt="User Icon">
   </div>
   <div class="menu" > 
      <span style=" color: black;"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
      <ul>
        
      <li><a href="manage-bookings.php">Manage Bookings</a></li>
      
      <li>
      <a href="?logout=true" class="btn">Logout</a>
        </li>
      </ul>
   </div>
    </div>
    <script>
      function menuToggle() {
        const toggleMenu = document.querySelector(".menu");
        toggleMenu.classList.toggle("active");
      }
    </script>

    <h1>Welcome to the Ticket Reservation System</h1>
    <p>Plan and manage your trips with ease</p>
  </header>

  <div class="container">
    <div class="grid">
      <div class="card">
        <h3>Search for Trains</h3>
        <p>Find available train options for your journey.</p>
        <a href="train-search.php" class="btn">Search Now</a>
      </div>
      <div class="card">
        <h3>Manage Bookings</h3>
        <p>View or modify your existing reservations.</p>
        <a href="manage-bookings.php" class="btn">Manage Bookings</a>
      </div>
      <div class="card">
        <h3>Support</h3>
        <p>Access essential features like ticket cancellation and support.</p>
        <a href="support.html" class="btn">View Support</a>
      </div>
    </div>
  </div>

  <footer>
    <p>&copy; 2025 Ticket Reservation System</p>
  </footer>
   
</body>
</html>
