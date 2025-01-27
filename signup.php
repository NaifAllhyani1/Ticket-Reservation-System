<?php
include 'db_connect.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up Form</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>
    <div class="logo-container">
        <img src="logo.png" alt="Website Logo">
    </div>

    <div class="container">
        <h1>Sign Up</h1>
        <form id="signupForm" action="" method="POST">
            <label for="fullname">Full Name</label>
            <input type="text" id="fullname" name="fullname" placeholder="Enter your full name" required pattern="^[a-zA-Z\s]+$" title="Full name should only contain letters and spaces.">
            
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" placeholder="Enter your email address" required>
            
            <label for="username">Username</label>
            <input type="text" id="username" name="username" placeholder="Choose a username" required pattern="^[a-zA-Z0-9_]{3,15}$" title="Username should be 3-15 characters long and can contain letters, numbers, and underscores.">
            
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Create a password" required minlength="6" title="Password must be at least 6 characters long.">
            
            <label for="confirm-password">Confirm Password</label>
            <input type="password" id="confirm-password" name="confirm-password" placeholder="Confirm your password" required>
            
            <button type="submit" name="signup">Sign Up</button>
        </form>

        <div class="confirmation">
            <a class="Sign" href="login.php"><button style=" margin-top: 10px;
  padding: 10px 20px; width: 1200px;" >Already have an account? Sign in</button> </a>
        </div>
    </div>

    <?php
    if (isset($_POST['signup'])) {
        $fullname = $_POST['fullname'];
        $email = $_POST['email'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirm-password'];

        if ($password !== $confirmPassword) {
            echo "<p style='color: red;'>Passwords do not match!</p>";
        } else {
            // Check if email or username already exists
            $checkSql = "SELECT * FROM users WHERE email = ? OR username = ?";
            $checkStmt = $conn->prepare($checkSql);
            $checkStmt->bind_param("ss", $email, $username);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();

            if ($checkResult->num_rows > 0) {
                echo "<p style='color: red;'>Email or Username already exists!</p>";
            } else {
                // Insert new user
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $sql = "INSERT INTO users (fullname, email, username, password_hash) VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssss", $fullname, $email, $username, $hashedPassword);

                if ($stmt->execute()) {
                    echo "<p style='color: green;'>Sign up successful! Redirecting to login page...</p>";
                    header("Refresh: 3; URL=login.php"); // Redirect after 3 seconds
                } else {
                    echo "<p style='color: red;'>Error: " . $stmt->error . "</p>";
                }
            }
        }
    }
    ?>
    
    <script>
        document.getElementById("signupForm").addEventListener("submit", function(event) {
            const password = document.getElementById("password").value;
            const confirmPassword = document.getElementById("confirm-password").value;

            if (password !== confirmPassword) {
                alert("Passwords do not match!");
                event.preventDefault(); // Stop form submission
            }
        });
    </script>
</body>
</html>
