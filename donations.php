<?php
require '../includes/auth.php';
require '../includes/config.php';

if (!isAdmin()) {
    header("Location: ../login.php");
    exit();
}

$stmt = $pdo->query("SELECT * FROM donations ORDER BY donation_date DESC");
$donations = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Donations</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <h1>Donation Management</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Donor ID</th>
                <th>Amount (ml)</th>
                <th>Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($donations as $donation): ?>
            <tr>
                <td><?= $donation['donation_id'] ?></td>
                <td><?= $donation['donor_id'] ?></td>
                <td><?= $donation['amount_ml'] ?></td>
                <td><?= $donation['donation_date'] ?></td>
                <td><?= $donation['status'] ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>