<?php
// Database connection settings
$servername = "localhost"; // Update this if needed
$username = "root"; // Database username
$password = ""; // Database password
$dbname = "nurture_land"; // Database name

// Create connection
$conn = new mysqli("localhost", "root", "", "nurture_land");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to sanitize input
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = sanitizeInput($_POST['name']);
    $address = sanitizeInput($_POST['address']);
    $contact = sanitizeInput($_POST['contact']);
    $email = sanitizeInput($_POST['email']);
    $password = sanitizeInput($_POST['password']);
    $confirm_password = sanitizeInput($_POST['confirm-password']);

    $errors = [];

    // Validation
    if (empty($name)) {
        $errors[] = "Name is required.";
    }
    
    if (empty($address)) {
        $errors[] = "Address is required.";
    }

    if (empty($contact) || !preg_match('/^[0-9]{10}$/', $contact)) {
        $errors[] = "Contact must be a valid 10-digit number.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long.";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    // If no validation errors, proceed with storing data
    if (empty($errors)) {
        // Hash the password before storing it
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Prepare and bind the SQL statement
        $stmt = $conn->prepare("INSERT INTO donors (name, address, contact, email, password) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $address, $contact, $email, $hashed_password);

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
        // Display validation errors in a single alert box
        $errorMessages = implode("\\n", $errors);
        echo "<script>
                alert('Validation Errors:\\n$errorMessages');
              </script>";
    }
}

$conn->close();
?>
