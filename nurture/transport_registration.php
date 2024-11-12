<?php
// Database connection settings
$servername = "localhost"; // Change if necessary
$username = "root"; // Database username
$password = ""; // Database password
$dbname = "nurture_land"; // Database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form data
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $address = filter_var($_POST['address'], FILTER_SANITIZE_STRING);
    $contact = filter_var($_POST['contact'], FILTER_SANITIZE_NUMBER_INT);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $vehicleNo = filter_var($_POST['vehicleNo'], FILTER_SANITIZE_STRING);
    $vehicleType = filter_var($_POST['vehicleType'], FILTER_SANITIZE_STRING);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm-password'];

    // Array to hold errors
    $errors = [];

    // Validate inputs
    if (empty($name)) {
        $errors[] = "Name is required.";
    }

    if (empty($address)) {
        $errors[] = "Address is required.";
    }

    if (empty($contact) || !preg_match('/^[0-9]{10}$/', $contact)) {
        $errors[] = "Contact number must be a valid 10-digit number.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (empty($vehicleNo)) {
        $errors[] = "Vehicle number is required.";
    }

    if (empty($vehicleType)) {
        $errors[] = "Vehicle type is required.";
    }

    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long.";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    // If no validation errors, proceed with data insertion
    if (empty($errors)) {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Prepare SQL statement
        $stmt = $conn->prepare("INSERT INTO transport (name, address, contact, email, vehicle_number, vehicle_type, password) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $name, $address, $contact, $email, $vehicleNo, $vehicleType, $hashed_password);

        // Execute and check if insertion was successful
        if ($stmt->execute()) {
            echo "<script>
                    alert('Registration successful!');
                    window.location.href = 'index.html';
                  </script>";
        } else {
            echo "<script>
                    alert('Error: " . $stmt->error . "');
                  </script>";
        }

        $stmt->close();
    } else {
        // Display validation errors in an alert box
        $errorMessages = implode("\\n", $errors);
        echo "<script>
                alert('Validation Errors:\\n$errorMessages');
              </script>";
    }
}

$conn->close();
?>
