<?php
// Database connection settings
$servername = "localhost"; // Change if needed
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
    // Get the input data from the login form
    $emailOrPhone = $_POST['email-phone'];
    $password = $_POST['password'];

    // Sanitize inputs
    $emailOrPhone = filter_var($emailOrPhone, FILTER_SANITIZE_STRING);
    
    // Query to fetch the user based on email or phone
    $stmt = $conn->prepare("SELECT id, email, contact, password FROM receivers WHERE email = ? OR contact = ?");
    $stmt->bind_param("ss", $emailOrPhone, $emailOrPhone);
    $stmt->execute();
    $stmt->store_result();
    
    // Check if the user exists
    if ($stmt->num_rows > 0) {
        // Bind result to variables
        $stmt->bind_result($id, $email, $contact, $hashed_password);
        $stmt->fetch();

        // Verify the password
        if (password_verify($password, $hashed_password)) {
            echo "<script>
                    alert('Login successful! Welcome, Receiver!');
                    window.location.href = 'index.html';
                  </script>";
        } else {
            echo "<script>
                    alert('Invalid password. Please try again.');
                  </script>";
        }
    } else {
        echo "<script>
                alert('No account found with that email or phone number.');
              </script>";
    }

    $stmt->close();
}

$conn->close();
?>
