<?php
include 'header.php';

if (!isset($_GET['train_id']) || !isset($_GET['selected_seats']) || empty($_GET['train_id']) || empty($_GET['selected_seats'])) {
    echo "<p style='color: red; text-align: center;'>Error: Missing train or seat details. Please go back and select a seat.</p>";
    echo "<p style='text-align: center;'><a href='train-search.php' class='btn'>Go to Train Search</a></p>";
    exit();
}

$train_id = intval($_GET['train_id']);
$selected_seats = htmlspecialchars($_GET['selected_seats']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Payment</title>
  <link rel="stylesheet" href="index.css">
</head>
<body>
  <header>
    <h1>Payment</h1>
  </header>
  <div class="container">
    <form method="POST" action="confirm_seat.php">
      <input type="hidden" name="train_id" value="<?php echo $train_id; ?>">
      <input type="hidden" name="selected_seats" value="<?php echo $selected_seats; ?>">

      <label for="card-name">Cardholder Name:</label>
      <input type="text" id="card-name" name="card_name" placeholder="Enter your name" required>

      <label for="card-number">Card Number:</label>
      <input type="text" id="card-number" name="card_number" placeholder="Enter card number" required>

      <label for="expiration-date">Expiration Date:</label>
      <input type="month" id="expiration-date" name="expiration_date" required>

      <label for="cvv">CVV:</label>
      <input type="text" id="cvv" name="cvv" placeholder="Enter CVV" required>

      <button type="submit" class="btn">Confirm Payment</button>
    </form>
  </div>
</body>
</html>
