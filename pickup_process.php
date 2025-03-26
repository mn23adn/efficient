<?php
session_start();
require "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // if (!isset($_SESSION['user_email'])) {
    //     echo json_encode(["status" => "error", "message" => "User not logged in!"]);
    //     exit();
    // }

    $user_email = $_POST["email"]; // Get the logged-in user's email
    $title = $_POST["title"];
    $type = $_POST["type"];
    $is_dangerous = $_POST["is_dangerous"];
    $size = $_POST["size"];
    $weight = $_POST["weight"];
    $address = $_POST["address"];
    $pincode = $_POST["pincode"];
    $latitude = $_POST["latitude"];
    $longitude = $_POST["longitude"];
    $vehicle_id = $_POST["vehicle_id"];

    // Image upload handling
    $targetDir = "uploads/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $image = $_FILES["image"]["name"];
    $imageTmp = $_FILES["image"]["tmp_name"];
    $imageExt = pathinfo($image, PATHINFO_EXTENSION);
    $newImageName = uniqid("pickup_", true) . "." . $imageExt;
    $imagePath = $targetDir . $newImageName;

    if (move_uploaded_file($imageTmp, $imagePath)) {
        $stmt = $conn->prepare("INSERT INTO pickups (user_email, title, type, is_dangerous, size, weight, image, address, pincode, latitude, longitude, vehicle_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssssssss", $user_email, $title, $type, $is_dangerous, $size, $weight, $newImageName, $address, $pincode, $latitude, $longitude, $vehicle_id);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Pickup request submitted successfully!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Database error. Please try again."]);
        }

        $stmt->close();
    } else {
        echo json_encode(["status" => "error", "message" => "Image upload failed."]);
    }
}
?>
