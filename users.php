<?php
require '../includes/auth.php';
require '../includes/config.php';
require '../includes/functions.php';

if (!isAdmin()) {
    header("Location: ../login.php");
    exit();
}

// Handle user actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_user'])) {
        $userId = (int)$_POST['user_id'];
        $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt->execute([$userId]);
        $_SESSION['success'] = "User deleted successfully";
    }
    
    if (isset($_POST['update_role'])) {
        $userId = (int)$_POST['user_id'];
        $newRole = $_POST['new_role'];
        $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE user_id = ?");
        $stmt->execute([$newRole, $userId]);
        $_SESSION['success'] = "User role updated successfully";
    }
}

// Get all users
$stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - Blood Donation System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .user-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .user-table th, .user-table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .user-table th {
            background-color: #f2f2f2;
        }
        .user-actions {
            display: flex;
            gap: 5px;
        }
        .role-select {
            padding: 5px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="admin-container">
        <h1>User Management</h1>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?= $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        
        <div class="user-filters">
            <form method="get" class="filter-form">
                <input type="text" name="search" placeholder="Search users..." value="<?= $_GET['search'] ?? '' ?>">
                <select name="role_filter">
                    <option value="">All Roles</option>
                    <option value="admin" <?= ($_GET['role_filter'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
                    <option value="donor" <?= ($_GET['role_filter'] ?? '') === 'donor' ? 'selected' : '' ?>>Donor</option>
                    <option value="requester" <?= ($_GET['role_filter'] ?? '') === 'requester' ? 'selected' : '' ?>>Requester</option>
                </select>
                <button type="submit" class="btn btn-primary">Filter</button>
            </form>
        </div>
        
        <table class="user-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Registered</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= $user['user_id'] ?></td>
                    <td><?= htmlspecialchars($user['username']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td>
                        <form method="POST" class="role-form">
                            <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">
                            <select name="new_role" class="role-select" onchange="this.form.submit()">
                                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                <option value="donor" <?= $user['role'] === 'donor' ? 'selected' : '' ?>>Donor</option>
                                <option value="requester" <?= $user['role'] === 'requester' ? 'selected' : '' ?>>Requester</option>
                            </select>
                            <input type="hidden" name="update_role" value="1">
                        </form>
                    </td>
                    <td><?= date('M d, Y', strtotime($user['created_at'])) ?></td>
                    <td><?= ucfirst($user['status']) ?></td>
                    <td class="user-actions">
                        <a href="view_user.php?id=<?= $user['user_id'] ?>" class="btn btn-secondary">View</a>
                        <form method="POST" onsubmit="return confirm('Are you sure you want to delete this user?')">
                            <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">
                            <button type="submit" name="delete_user" class="btn btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <?php include 'footer.php'; ?>
    
    <script>
        // Add confirmation for role changes
        document.querySelectorAll('.role-select').forEach(select => {
            select.addEventListener('change', function() {
                if (!confirm('Are you sure you want to change this user\'s role?')) {
                    this.form.reset();
                }
            });
        });
    </script>
</body>
</html>