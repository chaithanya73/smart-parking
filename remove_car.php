<?php
session_start(); // Start the session to check if the user is logged in

// Include database connection file
$conn = new mysqli('localhost', 'root', '', 'mydb');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "You are not logged in.";
    exit;
}

// Check if vehicle_id is provided in the URL
if (!isset($_GET['vehicle_id'])) {
    echo "No vehicle ID provided.";
    exit;
}

// Get the vehicle_id from the request
$vehicle_id = $_GET['vehicle_id'];

// Delete associated rows from the park_session table
$sql_delete_sessions = "DELETE FROM park_session WHERE VEHICLE_ID = ?";
$stmt_sessions = $conn->prepare($sql_delete_sessions);
if ($stmt_sessions) {
    $stmt_sessions->bind_param("s", $vehicle_id);
    $stmt_sessions->execute();
    $stmt_sessions->close();
} else {
    echo "Error preparing statement for park_session deletion: " . $conn->error;
    $conn->close();
    exit;
}

// Then, delete the vehicle itself
$sql_delete_vehicle = "DELETE FROM vehicles WHERE vehicle_id = ?";
$stmt_vehicle = $conn->prepare($sql_delete_vehicle);
if ($stmt_vehicle) {
    $stmt_vehicle->bind_param("s", $vehicle_id);
    if ($stmt_vehicle->execute()) {
        echo "Car removed successfully.";
    } else {
        echo "Error deleting car: " . $stmt_vehicle->error;
    }
    $stmt_vehicle->close();
} else {
    echo "Error preparing statement for vehicle deletion: " . $conn->error;
}

$conn->close();
?>
