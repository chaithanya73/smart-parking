<?php
session_start(); // Start the session to access session variables

// Include database connection file
$conn = new mysqli('localhost', 'root', '', 'mydb');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html"); // Redirect to login page if not logged in
    exit;
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate form inputs
    $carNumber = mysqli_real_escape_string($conn, $_POST['carNumber']);
    $carModel = mysqli_real_escape_string($conn, $_POST['carModel']);
    $carColor = mysqli_real_escape_string($conn, $_POST['carColor']);
    $carNickname = mysqli_real_escape_string($conn, $_POST['carNickname']);
    $userId = $_SESSION['user_id']; // Assume user ID is stored in session

    // Basic validation for car number (check if alphanumeric)
    if (!preg_match('/^[A-Za-z0-9]+$/', $carNumber)) {
        $error_message = "Please enter a valid car number (alphanumeric only).";
    } else {
        // Prepare and execute the insert query to add the car details to the database
        $sql = "INSERT INTO vehicles (user_id, vehicle_id, car_model, car_color, nickname) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $userId, $carNumber, $carModel, $carColor, $carNickname);

        if ($stmt->execute()) {
            $success_message = "Car details added successfully!";
        } else {
            $error_message = "Error adding car details. Please try again.";
        }
        $stmt->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add New Car</title>
  <link rel="stylesheet" href="addcar.css">
</head>
<body>

  <!-- Navbar -->
  <div class="navbar">
    <div class="logo">
      <img src="core_icon.webp" alt="SMART PARKING Icon">
      <span class="logo-text">SMART PARKING</span>
    </div>
  </div>

  <!-- Form container for adding car -->
  <div class="form-container">
    <h2>Add Your Car</h2>
    <?php if (isset($error_message)): ?>
      <div class="error-message"><?php echo $error_message; ?></div>
    <?php endif; ?>
    <?php if (isset($success_message)): ?>
      <div class="success-message"><?php echo $success_message; ?></div>
    <?php endif; ?>
    <form method="POST" action="add_car.php">
      <div class="form-group">
        <label for="carNumber">Car Number</label>
        <input type="text" id="carNumber" name="carNumber" required>
      </div>
      <div class="form-group">
        <label for="carModel">Car Model</label>
        <input type="text" id="carModel" name="carModel" required>
      </div>
      <div class="form-group">
        <label for="carColor">Car Color</label>
        <input type="text" id="carColor" name="carColor" required>
      </div>
      <div class="form-group">
        <label for="carNickname">Car Nickname</label>
        <input type="text" id="carNickname" name="carNickname" required>
      </div>
      <div class="form-group">
        <button type="submit">Add Car</button>
      </div>
    </form>
    <div class="back-link">
      <a href="homepage.html">Back to Home</a>
    </div>
  </div>

</body>
</html>
