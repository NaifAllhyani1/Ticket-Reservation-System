<?php
include 'header.php'; // Ensures session and user details are loaded
include 'db_connect.php'; // Database connection

// Check if train_id is provided in the URL
if (!isset($_GET['train_id']) || empty($_GET['train_id'])) {
    echo "<p style='color: red; text-align: center;'>Error: Train ID not provided. Please go back and select a train.</p>";
    echo "<p style='text-align: center;'><a href='train-search.php' class='btn'>Go to Train Search</a></p>";
    exit();
}

$train_id = intval($_GET['train_id']); // Sanitize train ID

// Fetch seat data for the specified train
$sql = "SELECT * FROM seats WHERE train_id = ? ORDER BY seat_number";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $train_id);
$stmt->execute();
$result = $stmt->get_result();
$seats = $result->fetch_all(MYSQLI_ASSOC);

// Handle form submission
$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id']; // User ID from session

    if (isset($_POST['selected_seats']) && !empty($_POST['selected_seats'])) {
        $selected_seats = $_POST['selected_seats']; // Comma-separated seat IDs

        // Redirect to payment page with selected seats and train ID
        header("Location: payment.php?train_id=$train_id&selected_seats=$selected_seats");
        exit();
    } else {
        $message = "No seats selected. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Seat Selection</title>
  <link rel="stylesheet" href="index2.css">
  
</head>
<body>
  <header>
    <h1>Seat Selection</h1>
  </header>
  <div class="container">
    <p style="color: green; text-align: center;"><?php echo htmlspecialchars($message); ?></p>
    
    <!-- Seat Type Filter -->
    <label for="seat-filter">Filter by seat type:</label>
    <select id="seat-filter">
      <option value="all">All</option>
      <option value="aisle">Aisle</option>
      <option value="window">Window</option>
    </select>
    
    <div class="seat-grid" id="seat-grid">
      <?php foreach ($seats as $seat): ?>
        <div class="seat <?php echo $seat['is_booked'] ? 'unavailable' : ''; ?>" 
             data-seat-id="<?php echo $seat['seat_id']; ?>"
             data-seat-type="<?php echo $seat['seat_type']; ?>">
          <?php echo $seat['seat_number']; ?>
        </div>
      <?php endforeach; ?>
    </div>
    <form method="POST">
      <input type="hidden" name="selected_seats" id="hidden-selected-seats">
      <input type="hidden" name="train_id" value="<?php echo $train_id; ?>">
      <button type="submit" class="btn">Proceed to Payment</button>
    </form>
  </div>
</body>
<script>
  const seatElements = document.querySelectorAll('.seat');
  const selectedSeatsInput = document.getElementById('hidden-selected-seats');
  const seatFilter = document.getElementById('seat-filter');
  const seatGrid = document.getElementById('seat-grid');

  let selectedSeats = [];

  // Handle seat click
  seatElements.forEach(seat => {
    seat.addEventListener('click', () => {
      if (seat.classList.contains('unavailable')) return;

      seat.classList.toggle('selected');
      const seatId = seat.getAttribute('data-seat-id');

      if (seat.classList.contains('selected')) {
        selectedSeats.push(seatId);
      } else {
        selectedSeats = selectedSeats.filter(s => s !== seatId);
      }

      selectedSeatsInput.value = selectedSeats.join(',');
    });
  });

  // Filter seats by type
  seatFilter.addEventListener('change', () => {
    const filter = seatFilter.value;
    seatElements.forEach(seat => {
      if (filter === 'all' || seat.dataset.seatType === filter) {
        seat.style.display = 'flex';
      } else {
        seat.style.display = 'none';
      }
    });
  });
</script>
</html>