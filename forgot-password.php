<?php
session_start();
include 'db_connect.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format';
    } else {
        $stmt = $conn->prepare("SELECT email FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $success = "Email is valid and exists in our system.";
        } else {
            $error = "Email not found";
        }
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Password</title>
  <link rel="stylesheet" href="index.css">
  <?php
    
    if (isset($success)) {
        echo "<script>
                setTimeout(function(){
                  window.location.href = 'login.php';
                }, 3000);
              </script>";
    }
  ?>
</head>
<body>
  <header>
    <div class="logo-container">
      <img src="logo.png" alt="Website Logo">
      
    </div>
    <div style="position: absolute; top: 60px; right: 20px;">
      <a href="login.php" class="btn">Back</a>
    </div>
  </header>
  <div class="container">
    <h2>Reset Password</h2>
    <?php if (isset($error)) { ?>
      <p class="error-message" style="display:block;"><?php echo $error; ?></p>
    <?php } ?>
    <?php if (isset($success)) { ?>
      <p class="confirmation-message" style="display:block;"><?php echo $success; ?></p>
    <?php } ?>
    <form method="post">
      <label for="email">Enter your email</label>
      <input type="email" name="email" required>
      <button type="submit">Send Reset Link</button>
    </form>
  </div>
  <footer>
    <p>&copy; 2025 Ticket Reservation System</p>
  </footer>
</body> 
</html>