<?php 
// Start session
session_start(); 
?> 

<!DOCTYPE html>
<html>
<head>
    <title>Register - Library Management System</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body class="centered">
    <div class="form-container">
        <h2>Register</h2>
        <!-- Registration form -->
        <form method="POST" action="register.php">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required><br>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required><br>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required><br>
            <button type="submit">Register</button>
        </form>
        <div>
            <!-- Display registration message if set in session -->
            <?php 
            if (isset($_SESSION['message'])) { 
                echo htmlspecialchars($_SESSION['message']);
                unset($_SESSION['message']);
            } 
            ?>
        </div>
    </div>
</body>
</html>

<?php
// Include database configuration file
require_once '../config/db.php';

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data and hash the password
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Validate form data
    if (empty($name) || empty($email) || empty($password)) {
        $_SESSION['message'] = "All fields are required.";
        header("Location: register.php");
        exit();
    }

    // Hash the password
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Prepare SQL statement to insert new user
    $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $passwordHash);

    // Execute statement and set session message based on success or failure
    if ($stmt->execute()) {
        // Store the new user ID in session
        $_SESSION['user_id'] = $stmt->insert_id;
        $_SESSION['message'] = "Registration successful!";
        // Redirect to add_book.php page
        header("Location: add_book.php");
        exit();
    } else {
        // Set error message
        $_SESSION['message'] = "Error: " . $stmt->error;
        // Redirect back to register.php page
        header("Location: register.php");
        exit();
    }

    // Close statement and database connection
    $stmt->close();
    $conn->close();
}
?>
