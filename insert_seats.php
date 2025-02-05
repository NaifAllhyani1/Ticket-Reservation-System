<?php
include 'db_connect.php'; // Database connection

// Check if train_id is provided
if (!isset($_GET['train_id']) || empty($_GET['train_id'])) {
    echo "Error: Train ID not provided.";
    exit();
}

$train_id = intval($_GET['train_id']); // Sanitize train ID

// Seat grid parameters
$rows = 5; // Number of rows
$columns = 5; // Number of seats per row
$seat_price = 20; // Default seat price
$seat_types = ['aisle', 'window']; // Available seat types

// Insert seats into the database
$sql = "INSERT INTO seats (train_id, seat_number, seat_type, is_booked, price) VALUES (?, ?, ?, 0, ?)";
$stmt = $conn->prepare($sql);

for ($row = 1; $row <= $rows; $row++) {
    for ($col = 1; $col <= $columns; $col++) {
        $seat_number = "{$row}-{$col}";
        $seat_type = $seat_types[array_rand($seat_types)]; // Random seat type
        $stmt->bind_param("isss", $train_id, $seat_number, $seat_type, $seat_price);
        $stmt->execute();
    }
}


echo "Seats successfully added for train ID $train_id.";
?>
