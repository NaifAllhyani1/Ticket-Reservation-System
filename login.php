<?php
include 'db_connect.php'; // Include database connection

// Enable error reporting for debugging (only in development, remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Initialize error message variable
$error_message = "";

// Start session
session_start();

// Check if the login form is submitted
if (isset($_POST['login'])) {
    // Sanitize and retrieve form inputs
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error_message = "Both fields are required.";
    } else {
        // Query to check if the user exists
        $sql = "SELECT user_id, password_hash FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();

                // Verify the password
                if (password_verify($password, $user['password_hash'])) {
                    // Store user ID in session and redirect to homepage
                    $_SESSION['user_id'] = $user['user_id'];
                    header("Location: homepage.php");
                    exit();
                } else {
                    $error_message = "Invalid username or password.";
                }
            } else {
                $error_message = "Invalid username or password.";
            }

            $stmt->close();
        } else {
            $error_message = "Database error. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>
    <div class="logo-container">
        <img src="logo.png" alt="Website Logo">
    </div>

    <div class="container">
        <h1>Login</h1>
        <form id="loginForm" action="" method="POST">
            <!-- Username Field -->
            <label for="username">Username</label>
            <input type="text" id="username" name="username" placeholder="Enter your username" required pattern="^[a-zA-Z0-9_]{3,15}$" title="Username should be 3-15 characters long and can contain letters, numbers, and underscores.">
            
            <!-- Password Field -->
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required minlength="6" title="Password must be at least 6 characters long.">
            
            <button type="submit" name="login">Login</button>
        </form>

        <div class="confirmation">
            <a href="forgot-password.php"><button style=" margin-top: 10px;
  padding: 10px 20px; width: 1200px;" > Forgot Password? </button></a>
        </div>

        <hr style="margin: 20px 0;">

        <div class="confirmation">
            <a href="signup.php"><button style=" margin-top: 10px;
  padding: 10px 20px; width: 1200px;" > Don't have an account? Sign Up </button></a>
        </div>
    </div>

    <!-- Display Error Message -->
    <?php
    if (!empty($error_message)) {
        echo "<p style='color: red; text-align: center;'>$error_message</p>";
    }
    ?>
</body>
</html>
