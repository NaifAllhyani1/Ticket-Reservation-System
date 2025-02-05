<?php
include 'header.php';
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $train_id = intval($_POST['train_id']);
    $selected_seats = htmlspecialchars($_POST['selected_seats']);
    $selected_seats_array = explode(',', $selected_seats);

    $sql = "INSERT INTO bookings (user_id, train_id, seat_id, booking_date, status, total_price) VALUES (?, ?, ?, NOW(), 'confirmed', ?)";
    $stmt = $conn->prepare($sql);

    foreach ($selected_seats_array as $seat_id) {
        // Fetch the price for the selected seat
        $price_sql = "SELECT price FROM seats WHERE seat_id = ?";
        $price_stmt = $conn->prepare($price_sql);
        $price_stmt->bind_param("i", $seat_id);
        $price_stmt->execute();
        $price_result = $price_stmt->get_result();
        $seat = $price_result->fetch_assoc();
        $price = $seat['price'];

        // Insert the booking record
        $stmt->bind_param("iiii", $user_id, $train_id, $seat_id, $price);
        $stmt->execute();

        // Mark the seat as booked
        $update_seat_sql = "UPDATE seats SET is_booked = 1 WHERE seat_id = ?";
        $update_seat_stmt = $conn->prepare($update_seat_sql);
        $update_seat_stmt->bind_param("i", $seat_id);
        $update_seat_stmt->execute();
    }

    echo "<p style='color: green; text-align: center;'>Booking confirmed! Redirecting to homepage...</p>";
    header("Refresh: 3; URL=homepage.php");
    exit();
} else {
    echo "<p style='color: red; text-align: center;'>Invalid request. Please start again.</p>";
    echo "<p style='text-align: center;'><a href='train-search.php' class='btn'>Go to Train Search</a></p>";
}

?>
