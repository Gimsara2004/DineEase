<?php
include 'config/db.php';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name    = $conn->real_escape_string(trim($_POST['name']    ?? ''));
    $email   = $conn->real_escape_string(trim($_POST['email']   ?? ''));
    $phone   = $conn->real_escape_string(trim($_POST['phone']   ?? ''));
    $message = $conn->real_escape_string(trim($_POST['message'] ?? ''));

    if (!$name || !$message) {
        echo json_encode(["status" => "error", "message" => "Name and message are required."]);
        exit();
    }

    $sql = "INSERT INTO messages (name, email, phone, message)
            VALUES ('$name','$email','$phone','$message')";

    if ($conn->query($sql)) {
        echo json_encode([
            "status"  => "success",
            "message" => "Thank you $name! We received your message and will get back to you soon."
        ]);
    } else {
        echo json_encode([
            "status"  => "error",
            "message" => "Something went wrong. Please try again."
        ]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request."]);
}
?>