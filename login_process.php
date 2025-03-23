<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get user input
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'mydb');
    if ($conn->connect_error) {
        die("Connection Failed: " . $conn->connect_error);
    }

    // Prepare and execute SQL statement to fetch user by email and password
    $stmt = $conn->prepare("SELECT user_id, email, password FROM registration WHERE email = ? AND password = ?");
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if user exists
    if ($result->num_rows > 0) {
        // Fetch user data
        $user = $result->fetch_assoc();
        
        // Store user_id in session
        $_SESSION['user_id'] = $user['user_id'];  // Store user_id instead of email
        
        echo '<div class="login-success-message">Login successful! Welcome back.</div>';
        
        // Redirect to dashboard or homepage
        header("Location: homepage.html"); // Correct syntax for redirection
        exit();
    } else {
        // Login failed
        echo '<div class="login-error-message">Invalid email or password. Please try again.</div>';
    }

    // Close connections
    $stmt->close();
    $conn->close();
}
?>
