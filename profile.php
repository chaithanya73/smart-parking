<?php
session_start(); // Start the session to access session variables

// Include database connection file
$conn = new mysqli('localhost', 'root', '', 'mydb');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html"); // Redirect to login page if not logged in
    exit;
}

// Fetch user details
$user_id = $_SESSION['user_id'];
$sql_user = "SELECT first_name, last_name, user_id, number_, email FROM registration WHERE user_id = ?";
$stmt = $conn->prepare($sql_user);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result_user = $stmt->get_result();
$user = $result_user->fetch_assoc();

// Fetch user's registered vehicles
$sql_vehicles = "SELECT nickname, vehicle_id, car_model, car_color FROM vehicles WHERE user_id = ?";
$stmt = $conn->prepare($sql_vehicles);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result_vehicles = $stmt->get_result();

$vehicles = [];
while ($row = $result_vehicles->fetch_assoc()) {
    $vehicles[] = $row;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profile - SMART PARKING</title>
  <link rel="stylesheet" href="profile.css">
</head>
<body>

  <!-- Navbar -->
  <div class="navbar">
    <div class="logo">
      <img src="core_icon.webp" alt="SMART PARKING Icon">
      <span class="logo-text">SMART PARKING</span>
    </div>
  </div>

  <!-- Profile Content -->
  <div class="profile-container">
    <h2>Your Profile</h2>
    
    <!-- User Name Shortcut -->
    <div class="profile-name-shortcut" id="profile-name-shortcut">
      <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>
    </div>

    <!-- User Information in Boxes -->
    <div class="profile-details">
      <div class="profile-box">
        <span class="label">Name</span>
        <span class="value"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></span>
      </div>
      <div class="profile-box">
        <span class="label">Registration ID</span>
        <span class="value"><?php echo htmlspecialchars($user['user_id']); ?></span>
      </div>
      <div class="profile-box">
        <span class="label">Phone Number</span>
        <span class="value"><?php echo htmlspecialchars($user['number_']); ?></span>
      </div>
      <div class="profile-box">
        <span class="label">Gmail ID</span>
        <span class="value"><?php echo htmlspecialchars($user['email']); ?></span>
      </div>
      <div class="profile-box">
  <span class="label">Registered Vehicles</span>
  <ul class="vehicles-list">
    <?php if (count($vehicles) === 0): ?>
      <li>You have no registered cars.</li>
    <?php else: ?>
      <?php foreach ($vehicles as $vehicle): ?>
        <li>
          <span class="nickname" onclick="toggleVehicleDetails(this)">
            <?php echo htmlspecialchars($vehicle['nickname']); ?>
            <button class="remove-btn" onclick="removeCar('<?php echo htmlspecialchars($vehicle['vehicle_id']); ?>'); event.stopPropagation();">Remove</button>
          </span>
          <div class="vehicle-details">
            <div>Car Number: <?php echo htmlspecialchars($vehicle['vehicle_id']); ?></div>
            <div>Car Model: <?php echo htmlspecialchars($vehicle['car_model']); ?></div>
            <div>Car Color: <?php echo htmlspecialchars($vehicle['car_color']); ?></div>
          </div>
        </li>
      <?php endforeach; ?>
    <?php endif; ?>
  </ul>
</div>


    <!-- Add New Car Button -->
    <a href="add_car.php" class="btn">Add New Car</a>
  </div>

  <script>
function toggleVehicleDetails(element) {
  const listItem = element.closest('li');
  listItem.classList.toggle('active');
}

function removeCar(vehicle_id) {
  if (confirm("Are you sure you want to remove this car ")) {
    fetch(`remove_car.php?vehicle_id=${encodeURIComponent(vehicle_id)}`, {
      method: 'GET',
    }).then(response => {
      return response.text().then(text => {
        if (response.ok) {
          alert(text); // Show success message
          location.reload(); // Reload the profile page after removal
        } else {
          alert(text); // Show error message
        }
      });
    }).catch(error => {
      alert("An error occurred: " + error.message);
    });
  }
}

</script>


</body>
</html>