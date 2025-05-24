<?php
require '../includes/auth.php';
require '../includes/config.php';

if (!isAdmin()) {
    header("Location: ../login.php");
    exit();
}

// Basic dashboard content
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <h1>Admin Dashboard</h1>
    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="donations.php">Donations</a>
        <a href="requests.php">Requests</a>
        <a href="../logout.php">Logout</a>
    </nav>
    
    <div class="content">
        <h2>Welcome, <?= htmlspecialchars($_SESSION['username']) ?></h2>
        <!-- Add your dashboard widgets here -->
    </div>
</body>
</html>
