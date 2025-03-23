<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html"); // Redirect to login page if not logged in
    exit();
}

$user_id = $_SESSION['user_id']; // Get the user_id from session

$conn = new mysqli('localhost', 'root', '@chaithussql', 'mydb');
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Mark a session as paid if 'pay' button is clicked
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['session_id'])) {
    $session_id = $_POST['session_id'];
    $update_sql = "UPDATE PARK_SESSION SET STATUS = 'paid' WHERE SESSION_ID = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("i", $session_id);
    $update_stmt->execute();
    $update_stmt->close();
    
    // Refresh the page to reflect changes
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// SQL query to fetch unpaid parking sessions for the specific user
$sql = "
    SELECT 
        PARK_SESSION.SESSION_ID AS session_id,
        VEHICLES.NICKNAME AS car_nickname,
        PARK_SESSION.PARK_DATE_START_TIME AS start_time,
        PARK_SESSION.PARK_DATE_END_TIME AS end_time,
        PARKING_LOTS.PARKING_LOT_NAME AS parking_lot,
        PARK_SESSION.STATUS AS status,
        PARK_SESSION.MONEY AS amount_due,
        VEHICLES.VEHICLE_ID AS vehicle_id,
        VEHICLES.CAR_MODEL AS car_model,
        VEHICLES.CAR_COLOR AS car_color
    FROM PARK_SESSION
    JOIN VEHICLES ON PARK_SESSION.VEHICLE_ID = VEHICLES.VEHICLE_ID
    JOIN PARKING_LOTS ON PARK_SESSION.PARKING_LOT_ID = PARKING_LOTS.PARKING_LOT_ID
    WHERE VEHICLES.USER_ID = ? AND PARK_SESSION.STATUS = 'unpaid'
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_id); // Bind the user_id
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Parking History - SMART PARKING</title>
  <link rel="stylesheet" href="pendingbills.css">
</head>
<body>

  <div class="container">
    <h2>Unpaid Parking Sessions</h2>
    <table class="table">
      <thead>
        <tr>
          <th>Car Nickname</th>
          <th>Start Time</th>
          <th>End Time</th>
          <th>Parking Lot</th>
          <th>Status</th>
          <th>Amount Due</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td>
                        <span class="nickname" onclick="toggleVehicleDetails('<?php echo $row['vehicle_id']; ?>')">
                            <?php echo htmlspecialchars($row['car_nickname']); ?>
                        </span>
                    </td>
                    <td><?php echo htmlspecialchars($row['start_time']); ?></td>
                    <td><?php echo htmlspecialchars($row['end_time']); ?></td>
                    <td><?php echo htmlspecialchars($row['parking_lot']); ?></td>
                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                    <td><?php echo "$" . htmlspecialchars($row['amount_due']); ?></td>
                    <td>
                        <form method="POST" action="">
                            <input type="hidden" name="session_id" value="<?php echo $row['session_id']; ?>">
                            <button type="submit" class="pay-btn">Pay</button>
                        </form>
                    </td>
                </tr>
                <tr class="vehicle-details" id="details-<?php echo $row['vehicle_id']; ?>">
                    <td colspan="7">
                        <div>
                            <strong>Car Number </strong> <?php echo htmlspecialchars($row['vehicle_id']); ?><br>
                            <strong>Car Model </strong> <?php echo htmlspecialchars($row['car_model']); ?><br>
                            <strong>Car Color </strong> <?php echo htmlspecialchars($row['car_color']); ?><br>
                        </div>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="7">No unpaid parking sessions found.</td>
            </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <script>
    function toggleVehicleDetails(vehicle_id) {
      const detailsRow = document.getElementById('details-' + vehicle_id);
      detailsRow.classList.toggle('active');
    }
  </script>

</body>
</html>

<?php 
// Close connections
$stmt->close();
$conn->close(); 
?>