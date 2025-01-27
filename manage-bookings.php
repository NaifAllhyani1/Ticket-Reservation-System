<?php
include 'header.php'; // Includes session and user details
include 'db_connect.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id']; // Logged-in user's ID
$message = ""; // Feedback message for booking actions

// Handle booking cancellation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'])) {
    $booking_id = $_POST['booking_id'];

    // Update booking status to "canceled"
    $sql = "UPDATE bookings SET status = 'canceled' WHERE booking_id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $booking_id, $user_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $message = "Booking successfully canceled.";
    } else {
        $message = "Failed to cancel the booking. Please try again.";
    }
}

// Fetch user bookings
$sql = "SELECT 
            b.booking_id, 
            t.train_name, 
            t.departure_location, 
            t.destination_location, 
            b.booking_date, 
            s.seat_number, 
            t.travel_date, 
            t.departure_time, 
            t.arrival_time,
            b.status,
            b.total_price
        FROM bookings b
        JOIN trains t ON b.train_id = t.train_id
        JOIN seats s ON b.seat_id = s.seat_id
        WHERE b.user_id = ?
        ORDER BY b.status, t.travel_date ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Separate bookings into confirmed and canceled
$confirmed_bookings = [];
$canceled_bookings = [];
while ($row = $result->fetch_assoc()) {
    if ($row['status'] === 'confirmed') {
        $confirmed_bookings[] = $row;
    } else {
        $canceled_bookings[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings</title>
    <link rel="stylesheet" href="Styles2.css">
    <style>
        .dropdown {
            margin-top: 20px;
            text-align: left;
        }

        .dropdown select {
            padding: 10px;
            font-size: 16px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .booking {
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 10px;
            background-color: #f9f9f9;
        }

        .canceled {
            display: none;
        }

        .container {
            margin: 20px auto;
            max-width: 800px;
        }
    </style>
    <script>
        function toggleBookings() {
            const status = document.getElementById('bookingFilter').value;
            const confirmed = document.querySelectorAll('.booking.confirmed');
            const canceled = document.querySelectorAll('.booking.canceled');

            if (status === 'confirmed') {
                confirmed.forEach(booking => booking.style.display = 'block');
                canceled.forEach(booking => booking.style.display = 'none');
            } else if (status === 'canceled') {
                confirmed.forEach(booking => booking.style.display = 'none');
                canceled.forEach(booking => booking.style.display = 'block');
            } else {
                confirmed.forEach(booking => booking.style.display = 'block');
                canceled.forEach(booking => booking.style.display = 'block');
            }
        }
    </script>
</head>
<body>
    <header>
        
        <form action="homepage.php" method="GET" style="display: inline;">
            <button type="submit" class="btn">Homepage</button>
        </form>
        <h1>Manage Your Bookings</h1>
    </header>
<!-- User Info Section -->
<div class="action">
   <div class="user-info" onclick="menuToggle();">
      <img src="user3.png" alt="User Icon">
   </div>
   <div class="menu" > 
      <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
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

    <div class="container">
        <!-- Feedback message -->
        <?php if (!empty($message)): ?>
            <p style="text-align: center; color: <?php echo strpos($message, 'successfully') !== false ? 'green' : 'red'; ?>;">
                <?php echo htmlspecialchars($message); ?>
            </p>
        <?php endif; ?>

        <!-- Dropdown to Filter Bookings -->
        <div class="dropdown">
            <label for="bookingFilter">Filter Bookings:</label>
            <select id="bookingFilter" onchange="toggleBookings()">
                <option value="confirmed" selected>Active Bookings</option>
                <option value="canceled">Canceled Bookings</option>
                <option value="all">All Bookings</option>
            </select>
        </div>

        <!-- Confirmed Bookings -->
        <?php foreach ($confirmed_bookings as $booking): ?>
            <div class="booking confirmed">
                <h3>Train: <?php echo htmlspecialchars($booking['train_name']); ?></h3>
                <p><strong>From:</strong> <?php echo htmlspecialchars($booking['departure_location']); ?></p>
                <p><strong>To:</strong> <?php echo htmlspecialchars($booking['destination_location']); ?></p>
                <p><strong>Travel Date:</strong> <?php echo htmlspecialchars($booking['travel_date']); ?></p>
                <p><strong>Departure:</strong> <?php echo htmlspecialchars($booking['departure_time']); ?></p>
                <p><strong>Arrival:</strong> <?php echo htmlspecialchars($booking['arrival_time']); ?></p>
                <p><strong>Seat:</strong> <?php echo htmlspecialchars($booking['seat_number']); ?></p>
                <p><strong>Price:</strong> $<?php echo htmlspecialchars($booking['total_price']); ?></p>
                <form method="POST">
                    <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
                    <button type="submit" class="btn cancel">Cancel Booking</button>
                </form>
            </div>
        <?php endforeach; ?>

        <!-- Canceled Bookings -->
        <?php foreach ($canceled_bookings as $booking): ?>
            <div class="booking canceled">
                <h3>Train: <?php echo htmlspecialchars($booking['train_name']); ?></h3>
                <p><strong>From:</strong> <?php echo htmlspecialchars($booking['departure_location']); ?></p>
                <p><strong>To:</strong> <?php echo htmlspecialchars($booking['destination_location']); ?></p>
                <p><strong>Travel Date:</strong> <?php echo htmlspecialchars($booking['travel_date']); ?></p>
                <p><strong>Departure:</strong> <?php echo htmlspecialchars($booking['departure_time']); ?></p>
                <p><strong>Arrival:</strong> <?php echo htmlspecialchars($booking['arrival_time']); ?></p>
                <p><strong>Seat:</strong> <?php echo htmlspecialchars($booking['seat_number']); ?></p>
                <p><strong>Price:</strong> $<?php echo htmlspecialchars($booking['total_price']); ?></p>
                <p style="color: gray;">This booking has been canceled.</p>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
