<?php
session_start();
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendOtpEmail($email, $otp) {
    $mail = new PHPMailer(true);

    try {
        // SMTP server settings
        $mail->isSMTP();
        $mail->SMTPDebug  = 0; // 0 for no debug output, 2 for detailed debug
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = '';
        $mail->Password   = ''; // Use app password if 2FA is enabled
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Sender and recipient settings
        $mail->setFrom('', 'Your Name');
        $mail->addAddress($email);

        // Email content
        $mail->isHTML(true);
        $mail->Subject = 'Your OTP Code';
        $mail->Body    = "Your OTP for registration is: $otp";

        $mail->send();
        echo '<div class="otp-success-message">OTP sent to your email successfully!</div>';
    } catch (Exception $e) {
        echo "Failed to send OTP. Error: {$mail->ErrorInfo}";
    }
}

// Main logic for handling form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['otp'])) {
        $enteredOtp = $_POST['otp'];

        if ($enteredOtp == $_SESSION['otp']) {
            // OTP is correct, proceed with registration
            $firstName = $_SESSION['firstName'];
            $lastName = $_SESSION['lastName'];
            $gender = $_SESSION['gender'];
            $email = $_SESSION['email'];
            $password = $_SESSION['password'];
            $number = $_SESSION['number'];

            // Database connection
            $conn = new mysqli('localhost', 'root', '@chaithussql', 'mydb');
            if ($conn->connect_error) {
                die("Connection Failed: " . $conn->connect_error);
            } else {
                do {
                    $user_id = substr(uniqid('user_', true), 0, 10); // Generates a unique ID
                    $checkStmt = $conn->prepare("SELECT COUNT(*) FROM registration WHERE user_id = ?");
                    $checkStmt->bind_param("s", $user_id);
                    $checkStmt->execute();
                    $checkStmt->bind_result($count);
                    $checkStmt->fetch();
                    $checkStmt->close();
                } while ($count > 0); // Ensure user_id is unique

                // Prepare and bind the statement for inserting the user
                $stmt = $conn->prepare("INSERT INTO registration (user_id, first_Name, last_Name, gender, email, password, number) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssssi", $user_id, $firstName, $lastName, $gender, $email, $password, $number);

                // Execute the statement
                $stmt->execute();
                echo '<div class="otp-success-message">Registration successfully completed.</div>
                <br><a href="index.html">Go to login page</a>';
                $stmt->close();
                $conn->close();
            
            }

            unset($_SESSION['otp']);
        } else {
            echo '<div class="otp-error-message">Invalid OTP. Please try again.</div>';
        }
    } else {
        // Collect registration form data
        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];
        $gender = $_POST['gender'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $number = $_POST['number'];

        // Generate OTP and store details in session
        $otp = rand(100000, 999999);
        $_SESSION['otp'] = $otp;
        $_SESSION['firstName'] = $firstName;
        $_SESSION['lastName'] = $lastName;
        $_SESSION['gender'] = $gender;
        $_SESSION['email'] = $email;
        $_SESSION['password'] = $password;
        $_SESSION['number'] = $number;

        // Send OTP email
        sendOtpEmail($email, $otp);

        // Display OTP verification form in a separate div
        echo '<div class="otp-form-container">
                <form method="post" action="" class="otp-form">
                    <label for="otp">Enter OTP sent to your email </label>
                    <input type="text" name="otp" required class="otp-input" />
                    <br>
                    <input type="submit" value="Verify OTP" class="btn otp-btn" />
                </form>
              </div>';
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Page</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your external CSS file -->
</head>
<body>
   
</body>
</html>
