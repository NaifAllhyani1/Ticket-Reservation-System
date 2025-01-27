<?php include 'header.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Train Search</title>
  <link rel="stylesheet" href="index2.css">
</head>
<body>
  
  <div class="container">
    <h1>Search for Trains</h1>

    <!-- Search Form -->
    <form id="train-search-form" method="POST" action="">
      <label for="departure-location">Departure Location:</label>
      <select id="departure-location" name="departure-location" required>
        <option value="" disabled selected>Select your departure location</option>
        <?php
        // Fetch unique departure locations from the database
        $sql = "SELECT DISTINCT departure_location FROM trains ORDER BY departure_location";
        $result = $conn->query($sql);
        while ($row = $result->fetch_assoc()) {
            $location = htmlspecialchars($row['departure_location']);
            echo "<option value=\"$location\">$location</option>";
        }
        ?>
      </select>

      <label for="destination-location">Destination Location:</label>
      <select id="destination-location" name="destination-location" required>
        <option value="" disabled selected>Select your destination location</option>
        <?php
        // Fetch unique destination locations from the database
        $sql = "SELECT DISTINCT destination_location FROM trains ORDER BY destination_location";
        $result = $conn->query($sql);
        while ($row = $result->fetch_assoc()) {
            $location = htmlspecialchars($row['destination_location']);
            echo "<option value=\"$location\">$location</option>";
        }
        ?>
      </select>

      <button type="submit" name="search">Search</button>
    </form>

    <!-- Available Dates -->
    <div id="available-dates" style="margin-top: 20px;">
      <?php
      if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search'])) {
          // Sanitize and retrieve form inputs
          $departure = trim($_POST['departure-location']);
          $destination = trim($_POST['destination-location']);

          // Validate inputs
          if (!empty($departure) && !empty($destination)) {
              // Query to fetch available travel dates for trains
              $sql = "SELECT DISTINCT travel_date FROM trains WHERE departure_location = ? AND destination_location = ? ORDER BY travel_date";
              $stmt = $conn->prepare($sql);
              $stmt->bind_param("ss", $departure, $destination);
              $stmt->execute();
              $result = $stmt->get_result();

              if ($result->num_rows > 0) {
                  echo "<h2>Available Dates</h2><ul>";
                  while ($row = $result->fetch_assoc()) {
                      $travel_date = htmlspecialchars($row['travel_date']); // Prevent XSS
                      echo "<li>
                              <form method='POST' action=''>
                                  <input type='hidden' name='departure-location' value='" . htmlspecialchars($departure) . "'>
                                  <input type='hidden' name='destination-location' value='" . htmlspecialchars($destination) . "'>
                                  <input type='hidden' name='travel_date' value='$travel_date'>
                                  <button type='submit' name='show_trains'>$travel_date</button>
                              </form>
                            </li>";
                  }
                  echo "</ul>";
              } else {
                  echo "<p style='color: red;'>No trains found for the selected locations.</p>";
              }
          } else {
              echo "<p style='color: red;'>Please select both locations.</p>";
          }
      }
      ?>
    </div>

    <!-- Train Results -->
    <div id="results" class="results" style="margin-top: 20px;">
      <?php
      if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['show_trains'])) {
          // Retrieve sanitized inputs from the form
          $departure = trim($_POST['departure-location']);
          $destination = trim($_POST['destination-location']);
          $travel_date = trim($_POST['travel_date']);

          // Query to fetch train details for the selected date
          $sql = "SELECT * FROM trains WHERE departure_location = ? AND destination_location = ? AND travel_date = ?";
          $stmt = $conn->prepare($sql);
          $stmt->bind_param("sss", $departure, $destination, $travel_date);
          $stmt->execute();
          $result = $stmt->get_result();

          if ($result->num_rows > 0) {
              echo "<h2>Trains Available on $travel_date</h2>";
              while ($row = $result->fetch_assoc()) {
                  $train_id = htmlspecialchars($row['train_id']); // Prevent XSS
                  $train_name = htmlspecialchars($row['train_name']);
                  $departure_time = htmlspecialchars($row['departure_time']);
                  $arrival_time = htmlspecialchars($row['arrival_time']);
                  $seats_available = htmlspecialchars($row['seats_available']);
                  
                  echo "<div style='border: 1px solid #ccc; border-radius: 8px; padding: 16px; margin-bottom: 10px; background-color: #f9f9f9;'>
                          <h3>Train Name: $train_name</h3>
                          <p><strong>Departure Time:</strong> $departure_time</p>
                          <p><strong>Arrival Time:</strong> $arrival_time</p>
                          <p><strong>Seats Available:</strong> $seats_available</p>
                          <a href='seat-selection.php?train_id=$train_id&travel_date=$travel_date&departure=" . urlencode($departure) . "&destination=" . urlencode($destination) . "' class='btn' style='margin-top: 10px; display: inline-block;'>Select Train</a>
                        </div>";
              }
          } else {
              echo "<p style='color: red;'>No trains found for the selected date.</p>";
          }
      }
      ?>
    </div>
  </div>
</body>
</html>
