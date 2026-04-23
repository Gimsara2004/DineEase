<?php
include 'config/db.php';
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name    = $conn->real_escape_string(trim($_POST['name']    ?? ''));
    $phone   = $conn->real_escape_string(trim($_POST['phone']   ?? ''));
    $email   = $conn->real_escape_string(trim($_POST['email']   ?? ''));
    $date    = $conn->real_escape_string(trim($_POST['date']    ?? ''));
    $time    = $conn->real_escape_string(trim($_POST['time']    ?? ''));
    $guests  = $conn->real_escape_string(trim($_POST['guests']  ?? '2')); // ← string now (handles "6+")
    $message = $conn->real_escape_string(trim($_POST['message'] ?? ''));

    if (!$name || !$phone || !$date || !$time) {
        echo json_encode(["status" => "error", "message" => "Please fill all required fields."]);
        exit();
    }

    $sql = "INSERT INTO reservations (name, phone, email, date, time, guests, message)
            VALUES ('$name','$phone','$email','$date','$time','$guests','$message')";

    if ($conn->query($sql)) {
        echo json_encode([
            "status"  => "success",
            "message" => "Thank you $name! Your reservation has been received. We will confirm shortly."
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