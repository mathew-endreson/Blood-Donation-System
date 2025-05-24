<?php
require '../includes/auth.php';
require '../includes/config.php';

if (!isAdmin()) {
    header("Location: ../login.php");
    exit();
}

$stmt = $pdo->query("SELECT * FROM blood_requests ORDER BY request_date DESC");
$requests = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Requests</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <h1>Blood Requests</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Patient</th>
                <th>Blood Type</th>
                <th>Amount</th>
                <th>Hospital</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($requests as $request): ?>
            <tr>
                <td><?= $request['request_id'] ?></td>
                <td><?= htmlspecialchars($request['patient_name']) ?></td>
                <td><?= $request['blood_type'] ?></td>
                <td><?= $request['amount_ml'] ?> ml</td>
                <td><?= htmlspecialchars($request['hospital_name']) ?></td>
                <td><?= $request['status'] ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
