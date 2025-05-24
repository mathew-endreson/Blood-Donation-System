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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    $status = $_POST['status'];
    
    // Validate inputs
    if (empty($username) || empty($email)) {
        $_SESSION['error'] = "Username and email are required";
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, role = ?, status = ? WHERE user_id = ?");
            $stmt->execute([$username, $email, $role, $status, $userId]);
            
            $_SESSION['success'] = "User updated successfully";
            header("Location: view_user.php?id=$userId");
            exit();
        } catch (PDOException $e) {
            $_SESSION['error'] = "Database error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - Blood Donation System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="admin-container">
        <h1>Edit User: <?= htmlspecialchars($user['username']) ?></h1>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" class="user-form">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required 
                       value="<?= htmlspecialchars($user['username']) ?>">
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required 
                       value="<?= htmlspecialchars($user['email']) ?>">
            </div>
            
            <div class="form-group">
                <label for="role">Role</label>
                <select id="role" name="role" required>
                    <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                    <option value="donor" <?= $user['role'] === 'donor' ? 'selected' : '' ?>>Donor</option>
                    <option value="requester" <?= $user['role'] === 'requester' ? 'selected' : '' ?>>Requester</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status" required>
                    <option value="active" <?= $user['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="suspended" <?= $user['status'] === 'suspended' ? 'selected' : '' ?>>Suspended</option>
                    <option value="pending" <?= $user['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                </select>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="view_user.php?id=<?= $user['user_id'] ?>" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
    
    <?php include 'footer.php'; ?>
</body>
</html>
