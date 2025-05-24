<?php
require '../includes/auth.php';
require '../includes/config.php';

if (!isAdmin()) {
    header("Location: ../login.php");
    exit();
}

$userId = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    $_SESSION['error'] = "User not found";
    header("Location: users.php");
    exit();
}

// Get additional info based on role
if ($user['role'] === 'donor') {
    $stmt = $pdo->prepare("SELECT * FROM donors WHERE user_id = ?");
    $stmt->execute([$userId]);
    $profile = $stmt->fetch(PDO::FETCH_ASSOC);
} elseif ($user['role'] === 'requester') {
    $stmt = $pdo->prepare("SELECT * FROM blood_requests WHERE user_id = ? ORDER BY request_date DESC LIMIT 5");
    $stmt->execute([$userId]);
    $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View User - Blood Donation System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="admin-container">
        <h1>User Details: <?= htmlspecialchars($user['username']) ?></h1>
        
        <div class="user-profile">
            <div class="profile-section">
                <h2>Basic Information</h2>
                <p><strong>Username:</strong> <?= htmlspecialchars($user['username']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
                <p><strong>Role:</strong> <?= ucfirst($user['role']) ?></p>
                <p><strong>Status:</strong> <?= ucfirst($user['status']) ?></p>
                <p><strong>Registered:</strong> <?= date('M d, Y H:i', strtotime($user['created_at'])) ?></p>
                <p><strong>Last Login:</strong> <?= $user['last_login'] ? date('M d, Y H:i', strtotime($user['last_login'])) : 'Never' ?></p>
            </div>
            
            <?php if ($user['role'] === 'donor' && isset($profile)): ?>
            <div class="profile-section">
                <h2>Donor Profile</h2>
                <p><strong>Name:</strong> <?= htmlspecialchars($profile['first_name'] . ' ' . $profile['last_name']) ?></p>
                <p><strong>Blood Type:</strong> <?= $profile['blood_type'] ?></p>
                <p><strong>Last Donation:</strong> <?= $profile['last_donation_date'] ?? 'Never' ?></p>
                <p><strong>Phone:</strong> <?= htmlspecialchars($profile['phone']) ?></p>
                <p><strong>Address:</strong> <?= htmlspecialchars($profile['address']) ?></p>
            </div>
            <?php endif; ?>
            
            <?php if ($user['role'] === 'requester' && isset($requests)): ?>
            <div class="profile-section">
                <h2>Recent Blood Requests</h2>
                <?php if ($requests): ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Request ID</th>
                                <th>Blood Type</th>
                                <th>Amount (ml)</th>
                                <th>Hospital</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($requests as $request): ?>
                            <tr>
                                <td><?= $request['request_id'] ?></td>
                                <td><?= $request['blood_type'] ?></td>
                                <td><?= $request['amount_ml'] ?></td>
                                <td><?= htmlspecialchars($request['hospital_name']) ?></td>
                                <td><?= ucfirst($request['status']) ?></td>
                                <td><?= date('M d, Y', strtotime($request['request_date'])) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No blood requests found.</p>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="action-buttons">
            <a href="users.php" class="btn btn-secondary">Back to Users</a>
            <a href="edit_user.php?id=<?= $user['user_id'] ?>" class="btn btn-primary">Edit User</a>
        </div>
    </div>
    
    <?php include 'footer.php'; ?>
</body>
</html>
