<?php
require 'includes/auth.php';
require 'includes/config.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header("Location: " . (isAdmin() ? 'admin/dashboard.php' : (isDonor() ? 'donor/dashboard.php' : 'requester/dashboard.php')));
    exit();
}

$error = '';
$success = isset($_GET['logout']) && $_GET['logout'] === 'success' ? 'You have been successfully logged out.' : '';
$success = isset($_GET['registered']) && $_GET['registered'] === 'success' ? 'Registration successful! Please log in.' : $success;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $remember = isset($_POST['remember']);

    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password';
    } else {
        if (login($username, $password)) {
            // Set remember me cookie if selected
            if ($remember) {
                $token = bin2hex(random_bytes(32));
                $expiry = time() + 60 * 60 * 24 * 30; // 30 days
                
                setcookie('remember_token', $token, $expiry, '/');
                
                // Store token in database
                $stmt = $pdo->prepare("UPDATE users SET remember_token = ?, token_expiry = ? WHERE user_id = ?");
                $stmt->execute([$token, date('Y-m-d H:i:s', $expiry), $_SESSION['user_id']]);
            }

            // Redirect to appropriate dashboard
            $redirectPage = isAdmin() ? 'admin/dashboard.php' : (isDonor() ? 'donor/dashboard.php' : 'requester/dashboard.php');
            header("Location: $redirectPage");
            exit();
        } else {
            $error = 'Invalid username or password';
        }
    }
}

// Check for remember me cookie
if (empty($error)) {
    if (isset($_COOKIE['remember_token'])) {
        $token = $_COOKIE['remember_token'];
        $stmt = $pdo->prepare("SELECT * FROM users WHERE remember_token = ? AND token_expiry > NOW()");
        $stmt->execute([$token]);
        $user = $stmt->fetch();

        if ($user) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            header("Location: " . (isAdmin() ? 'admin/dashboard.php' : (isDonor() ? 'donor/dashboard.php' : 'requester/dashboard.php')));
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Blood Donation System</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .login-container {
            max-width: 500px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transform: perspective(1000px);
            transition: all 0.5s ease;
        }

        .login-container:hover {
            transform: perspective(1000px) rotateY(5deg);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-header i {
            font-size: 3rem;
            color: #e74c3c;
            margin-bottom: 15px;
            animation: heartbeat 1.5s infinite;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            border-color: #e74c3c;
            box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.2);
            outline: none;
        }

        .form-group .input-icon {
            position: absolute;
            right: 15px;
            top: 40px;
            color: #999;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .checkbox-group input {
            margin-right: 10px;
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            background: #e74c3c;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            background: #c0392b;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(231, 76, 60, 0.4);
        }

        .login-footer {
            text-align: center;
            margin-top: 20px;
        }

        .login-footer a {
            color: #e74c3c;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .login-footer a:hover {
            text-decoration: underline;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            animation: fadeIn 0.5s ease-out;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        @keyframes heartbeat {
            0% { transform: scale(1); }
            25% { transform: scale(1.1); }
            50% { transform: scale(1); }
            75% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 40px;
            cursor: pointer;
            color: #999;
            transition: all 0.3s ease;
        }

        .password-toggle:hover {
            color: #e74c3c;
        }

        .login-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background: linear-gradient(135deg, rgba(231, 76, 60, 0.1), rgba(192, 57, 43, 0.1));
        }

        .login-bg .drop {
            position: absolute;
            background: rgba(231, 76, 60, 0.1);
            border-radius: 50%;
            animation: float 15s infinite linear;
        }

        .login-bg .drop:nth-child(1) {
            width: 200px;
            height: 200px;
            top: 10%;
            left: 10%;
            animation-delay: 0s;
        }

        .login-bg .drop:nth-child(2) {
            width: 300px;
            height: 300px;
            top: 50%;
            left: 70%;
            animation-delay: 5s;
        }

        .login-bg .drop:nth-child(3) {
            width: 150px;
            height: 150px;
            top: 80%;
            left: 30%;
            animation-delay: 10s;
        }

        @keyframes float {
            0% {
                transform: translateY(0) rotate(0deg);
                opacity: 1;
            }
            100% {
                transform: translateY(-1000px) rotate(720deg);
                opacity: 0;
            }
        }
    </style>
</head>
<body class="preload">
    <div class="login-bg">
        <div class="drop"></div>
        <div class="drop"></div>
        <div class="drop"></div>
    </div>

    <div class="container" style="min-height: 100vh; display: flex; align-items: center; justify-content: center;">
        <div class="login-container animate__animated animate__fadeInUp">
            <div class="login-header">
                <i class="fas fa-tint"></i>
                <h2>Welcome Back</h2>
                <p>Sign in to your account</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger animate__animated animate__shakeX">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success animate__animated animate__fadeIn">
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <form method="POST" id="loginForm">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required
                           value="<?= htmlspecialchars($username ?? '') ?>"
                           class="<?= $error && empty($username) ? 'input-error' : '' ?>">
                    <i class="fas fa-user input-icon"></i>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required
                           class="<?= $error && empty($password) ? 'input-error' : '' ?>">
                    <i class="fas fa-lock input-icon"></i>
                    <span class="password-toggle" id="togglePassword">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>

                <div class="checkbox-group">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Remember me</label>
                </div>

                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>
            </form>

            <div class="login-footer">
                <p>Don't have an account? <a href="register.php" class="animate__animated animate__fadeIn animate__delay-1s">Register here</a></p>
                <p><a href="forgot-password.php" class="animate__animated animate__fadeIn animate__delay-1s">Forgot password?</a></p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Remove preload class after page loads
            setTimeout(function() {
                document.body.classList.remove('preload');
            }, 500);

            // Password toggle
            const togglePassword = document.getElementById('togglePassword');
            const password = document.getElementById('password');
            
            togglePassword.addEventListener('click', function() {
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
            });

            // Form submission animation
            const loginForm = document.getElementById('loginForm');
            
            loginForm.addEventListener('submit', function() {
                const btn = this.querySelector('button[type="submit"]');
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Authenticating...';
                btn.disabled = true;
            });

            // Animate elements sequentially
            const animateElements = [
                '.login-header i',
                '.login-header h2',
                '.login-header p',
                '.form-group:nth-child(1)',
                '.form-group:nth-child(2)',
                '.checkbox-group',
                '.btn-login',
                '.login-footer'
            ];

            animateElements.forEach((selector, index) => {
                const element = document.querySelector(selector);
                if (element) {
                    element.style.animationDelay = `${index * 0.1}s`;
                    element.classList.add('animate__animated', 'animate__fadeInUp');
                }
            });

            // Blood drop animation for login button
            const loginBtn = document.querySelector('.btn-login');
            
            loginBtn.addEventListener('mouseenter', function() {
                createBloodDrops(3);
            });

            function createBloodDrops(count) {
                const container = document.querySelector('.login-container');
                
                for (let i = 0; i < count; i++) {
                    const drop = document.createElement('div');
                    drop.classList.add('blood-drop-effect');
                    drop.style.left = Math.random() * 100 + '%';
                    drop.style.animationDuration = (Math.random() * 1 + 0.5) + 's';
                    container.appendChild(drop);
                    
                    setTimeout(() => {
                        drop.remove();
                    }, 1500);
                }
            }
        });

        // Add styles for blood drop effect
        const style = document.createElement('style');
        style.textContent = `
            .blood-drop-effect {
                position: absolute;
                width: 10px;
                height: 10px;
                background-color: rgba(231, 76, 60, 0.7);
                border-radius: 50%;
                pointer-events: none;
                animation: drop-fall 1.5s linear forwards;
                z-index: -1;
            }
            
            @keyframes drop-fall {
                0% {
                    transform: translateY(-20px);
                    opacity: 1;
                }
                100% {
                    transform: translateY(100px);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>
