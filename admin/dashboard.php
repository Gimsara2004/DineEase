<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
include '../config/db.php';

$reservations = $conn->query(
    "SELECT * FROM reservations 
     ORDER BY date DESC, created_at DESC"
);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
</head>
<body>
    <h2>All Reservations</h2>
    <table border="1" cellpadding="10">
        <tr>
            <th>Name</th>
            <th>Phone</th>
            <th>Date</th>
            <th>Time</th>
            <th>Guests</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $reservations->fetch_assoc()): ?>
        <tr>
            <td><?= $row['name'] ?></td>
            <td><?= $row['phone'] ?></td>
            <td><?= $row['date'] ?></td>
            <td><?= $row['time'] ?></td>
            <td><?= $row['guests'] ?></td>
            <td><?= $row['status'] ?></td>
            <td>
                <a href="confirm.php?id=<?= $row['id'] ?>">
                    Confirm
                </a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>