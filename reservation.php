<?php
include 'config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name    = $_POST['name'];
    $phone   = $_POST['phone'];
    $email   = $_POST['email'];
    $date    = $_POST['date'];
    $time    = $_POST['time'];
    $guests  = $_POST['guests'];
    $message = $_POST['message'];

    $stmt = $conn->prepare("INSERT INTO reservations 
        (name, phone, email, date, time, guests, message) 
        VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->bind_param("sssssss", 
        $name, $phone, $email, 
        $date, $time, $guests, $message);
    
    if ($stmt->execute()) {
        echo json_encode([
            "status"  => "success",
            "message" => "Reservation confirmed!"
        ]);
    } else {
        echo json_encode([
            "status"  => "error",
            "message" => "Something went wrong."
        ]);
    }
}
?>git 