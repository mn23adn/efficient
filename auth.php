<?php
session_start();
require "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['register'])) {
        // User Registration
        $full_name = $_POST['full_name'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $latitude = $_POST['latitude'] ?? NULL;
        $longitude = $_POST['longitude'] ?? NULL;

        // Check if email already exists
        $checkQuery = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $checkQuery->bind_param("s", $email);
        $checkQuery->execute();
        $result = $checkQuery->get_result();

        if ($result->num_rows > 0) {
            echo json_encode(["status" => "error", "message" => "Email already exists!"]);
        } else {
            // Insert user with latitude & longitude
            $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, latitude, longitude) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $full_name, $email, $password, $latitude, $longitude);

            if ($stmt->execute()) {
                echo json_encode(["status" => "success", "message" => "Registration successful!"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Registration failed!"]);
            }

            $stmt->close();
        }
    } elseif (isset($_POST['login'])) {
        // User Login
        $email = $_POST['email'];
        $password = $_POST['password'];
    
        $stmt = $conn->prepare("SELECT id, full_name, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($id, $full_name, $hashed_password);
        
        if ($stmt->num_rows > 0) {
            $stmt->fetch();
            if (password_verify($password, $hashed_password)) {
                $_SESSION['user'] = $full_name;
                $_SESSION['user_email'] = $email; // Store user email in session
                echo json_encode(["status" => "success", "message" => "Login successful!"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Invalid password!"]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "User not found!"]);
        }
    
        $stmt->close();
    }
    
}
?>
