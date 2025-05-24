<?php
require 'includes/auth.php';
require 'includes/config.php';

$error = '';
$success = '';

// Set default role from URL parameter if present
$defaultRole = isset($_GET['role']) && in_array($_GET['role'], ['donor', 'requester']) ? $_GET['role'] : 'requester';
$isEmergency = isset($_GET['emergency']) && $_GET['emergency'] == '1';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $role = $_POST['role'];
    $agreeTerms = isset($_POST['agree_terms']);

    // Validate inputs
    if (empty($username) || empty($email) || empty($password)) {
        $error = 'Please fill in all required fields';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters long';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address';
    } elseif (!$agreeTerms) {
        $error = 'You must agree to the terms and conditions';
    } else {
        try {
            // Check if username or email already exists
            $stmt = $pdo->prepare("SELECT user_id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            
            if ($stmt->rowCount() > 0) {
                $error = 'Username or email already exists';
            } else {
                // Register the user
                if (registerUser($username, $password, $email, $role)) {
                    $success = 'Registration successful!';
                    
                    // If emergency registration, redirect to request form
                    if ($isEmergency) {
                        header("Location: requester/dashboard.php?emergency=1");
                        exit();
                    }
                    
                    // Redirect to appropriate dashboard
                    $redirectPage = $role === 'donor' ? 'donor/dashboard.php' : 'requester/dashboard.php';
                    header("Location: $redirectPage");
                    exit();
                } else {
                    $error = 'Registration failed. Please try again.';
                }
            }
        } catch (PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Blood Donation System</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="preload">
    <div class="background-animation">
        <div class="blood-drop"></div>
        <div class="blood-drop"></div>
        <div class="blood-drop"></div>
    </div>

    <header class="animate__animated animate__fadeInDown">
        <div class="container">
            <h1>
                <i class="fas fa-tint heartbeat"></i>
                <span class="title-text">Register Account</span>
            </h1>
            <p class="tagline animate__animated animate__fadeIn animate__delay-1s">Join our life-saving community</p>
        </div>
    </header>

    <main class="container animate__animated animate__fadeIn animate__delay-1s">
        <section class="registration-form">
            <div class="form-container animate__animated animate__fadeInUp">
                <?php if ($error): ?>
                    <div class="alert alert-danger animate__animated animate__shakeX">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <?= htmlspecialchars($success) ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" id="registrationForm">
                    <div class="form-group">
                        <label for="username"><i class="fas fa-user"></i> Username</label>
                        <input type="text" id="username" name="username" required 
                               class="<?= isset($error) && empty($username) ? 'input-error' : '' ?>"
                               value="<?= htmlspecialchars($username ?? '') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="email"><i class="fas fa-envelope"></i> Email</label>
                        <input type="email" id="email" name="email" required
                               class="<?= isset($error) && (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) ? 'input-error' : '' ?>"
                               value="<?= htmlspecialchars($email ?? '') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="password"><i class="fas fa-lock"></i> Password</label>
                        <input type="password" id="password" name="password" required
                               class="<?= isset($error) && (empty($password) || strlen($password) < 8) ? 'input-error' : '' ?>">
                        <small>Minimum 8 characters</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password"><i class="fas fa-lock"></i> Confirm Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" required
                               class="<?= isset($error) && $password !== $confirmPassword ? 'input-error' : '' ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="role"><i class="fas fa-user-tag"></i> Register As</label>
                        <select id="role" name="role" required>
                            <option value="donor" <?= $defaultRole === 'donor' ? 'selected' : '' ?>>Blood Donor</option>
                            <option value="requester" <?= $defaultRole === 'requester' ? 'selected' : '' ?>>Blood Requester</option>
                        </select>
                    </div>
                    
                    <div class="form-group checkbox-group">
                        <input type="checkbox" id="agree_terms" name="agree_terms" required
                               <?= isset($agreeTerms) && $agreeTerms ? 'checked' : '' ?>>
                        <label for="agree_terms">I agree to the <a href="#" id="termsLink">Terms and Conditions</a></label>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-user-plus"></i> Register
                        </button>
                        <a href="login.php" class="btn btn-secondary">
                            <i class="fas fa-sign-in-alt"></i> Already have an account?
                        </a>
                    </div>
                </form>
            </div>
            
            <div class="registration-info animate__animated animate__fadeInRight">
                <div class="info-card">
                    <h3><i class="fas fa-tint"></i> Why Register?</h3>
                    <ul>
                        <li>Join a life-saving community</li>
                        <li>Get notifications for blood needs</li>
                        <li>Track your donation history</li>
                        <li>Earn rewards for regular donations</li>
                    </ul>
                </div>
                
                <div class="info-card emergency <?= $isEmergency ? 'pulse' : '' ?>">
                    <h3><i class="fas fa-ambulance"></i> Emergency?</h3>
                    <p>If you need blood urgently, our system will prioritize your request.</p>
                    <?php if (!$isEmergency): ?>
                        <a href="register.php?role=requester&emergency=1" class="btn btn-danger">
                            <i class="fas fa-bolt"></i> Emergency Registration
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </main>

    <!-- Terms and Conditions Modal -->
    <div id="termsModal" class="modal">
        <div class="modal-content animate__animated animate__fadeInUp">
            <span class="close-modal">&times;</span>
            <h2>Terms and Conditions</h2>
            <div class="terms-content">
                <!-- Your terms and conditions content here -->
                <p>By registering with our Blood Donation System, you agree to the following terms:</p>
                <ol>
                    <li>You must provide accurate information during registration.</li>
                    <li>Donors must meet all health requirements for blood donation.</li>
                    <li>Requesters must use the system only for legitimate medical needs.</li>
                    <li>Your personal data will be protected according to our privacy policy.</li>
                    <li>The system administrators reserve the right to verify any information provided.</li>
                </ol>
            </div>
        </div>
    </div>

    <script src="assets/js/main.js"></script>
    <script>
        // Terms and conditions modal
        const termsLink = document.getElementById('termsLink');
        const termsModal = document.getElementById('termsModal');
        const closeModal = document.querySelector('.close-modal');
        
        termsLink.addEventListener('click', function(e) {
            e.preventDefault();
            termsModal.style.display = 'block';
        });
        
        closeModal.addEventListener('click', function() {
            termsModal.style.display = 'none';
        });
        
        window.addEventListener('click', function(e) {
            if (e.target === termsModal) {
                termsModal.style.display = 'none';
            }
        });
        
        // Password strength indicator
        const passwordInput = document.getElementById('password');
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            const strengthIndicator = document.getElementById('password-strength');
            
            // You could add a password strength meter here
        });
        
        // Role selection effects
        const roleSelect = document.getElementById('role');
        roleSelect.addEventListener('change', function() {
            const infoCards = document.querySelectorAll('.info-card');
            
            if (this.value === 'donor') {
                infoCards[0].classList.add('highlight');
                setTimeout(() => infoCards[0].classList.remove('highlight'), 1000);
            } else {
                infoCards[1].classList.add('highlight');
                setTimeout(() => infoCards[1].classList.remove('highlight'), 1000);
            }
        });
    </script>
</body>
</html>
