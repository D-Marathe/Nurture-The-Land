<?php
// Database connection settings
$servername = "localhost"; // Update with your server information if necessary
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
    // Retrieve form data
    $email_or_phone = $_POST['email-phone'];
    $password = $_POST['password'];

    // Prepare and execute the SQL query
    $stmt = $conn->prepare("SELECT password FROM transport WHERE email = ? OR contact = ?");
    $stmt->bind_param("ss", $email_or_phone, $email_or_phone); // Bind the same variable to both email and contact fields

    // Execute the query
    $stmt->execute();
    $stmt->store_result();

    // Check if a user exists with the given email or phone number
    if ($stmt->num_rows > 0) {
        // Bind result to variables
        $stmt->bind_result($stored_password);
        $stmt->fetch();

        // Verify the password
        if (password_verify($password, $stored_password)) {
            // Login successful, display alert and redirect to index.html
            echo "<script>
                    alert('Login successful! Redirecting to the homepage...');
                    window.location.href = 'index.html';
                  </script>";
        } else {
            // Invalid password, show an alert message
            echo "<script>
                    alert('Invalid password. Please try again.');
                  </script>";
        }
    } else {
        // No user found, show an alert message
        echo "<script>
                alert('No account found with the provided email or phone number.');
              </script>";
    }

    // Close statement
    $stmt->close();
}

// Close connection
$conn->close();
?>
