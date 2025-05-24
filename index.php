<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Donation System</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
                <span class="title-text">Blood Donation System</span>
            </h1>
            <p class="tagline animate__animated animate__fadeIn animate__delay-1s">Saving lives one donation at a time</p>
        </div>
    </header>

    <nav class="animate__animated animate__fadeIn animate__delay-1s">
        <div class="container">
            <ul>
                <li><a href="index.php" class="nav-link active" data-section="home"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="login.php" class="nav-link" data-section="login"><i class="fas fa-sign-in-alt"></i> Login</a></li>
                <li><a href="register.php" class="nav-link" data-section="register"><i class="fas fa-user-plus"></i> Register</a></li>
                <li><a href="#about" class="nav-link" data-section="about"><i class="fas fa-info-circle"></i> About</a></li>
            </ul>
        </div>
    </nav>

    <main class="container animate__animated animate__fadeIn animate__delay-1s">
        <section id="home" class="content-section active">
            <div class="hero">
                <div class="hero-text">
                    <h2 class="animate__animated animate__fadeInLeft">Donate Blood, Save Lives</h2>
                    <p class="animate__animated animate__fadeInLeft animate__delay-1s">Join our community of donors and help those in need</p>
                    <div class="cta-buttons animate__animated animate__fadeInUp animate__delay-2s">
                        <a href="register.php?role=donor" class="btn btn-primary pulse"><i class="fas fa-tint"></i> Become a Donor</a>
                        <a href="register.php?role=requester" class="btn btn-secondary"><i class="fas fa-hand-holding-medical"></i> Request Blood</a>
                    </div>
                </div>
                <div class="hero-image animate__animated animate__fadeInRight animate__delay-1s">
                    <img src="assets/images/blood-donation-illustration.png" alt="Blood donation illustration">
                </div>
            </div>

            <div class="stats-container animate__animated animate__fadeInUp animate__delay-2s">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-value" id="totalDonors">10,000+</div>
                    <div class="stat-label">Registered Donors</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-heartbeat"></i>
                    </div>
                    <div class="stat-value" id="totalDonations">50,000+</div>
                    <div class="stat-label">Donations Made</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-life-ring"></i>
                    </div>
                    <div class="stat-value" id="livesSaved">100,000+</div>
                    <div class="stat-label">Lives Saved</div>
                </div>
            </div>
        </section>

        <section id="about" class="content-section">
            <h2 class="section-title animate__animated animate__fadeIn">About Our System</h2>
            <div class="about-content">
                <div class="about-card animate__animated animate__fadeInLeft">
                    <div class="about-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3>Safe & Secure</h3>
                    <p>Our system ensures the highest standards of data security and privacy for all users.</p>
                </div>
                <div class="about-card animate__animated animate__fadeInUp">
                    <div class="about-icon">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <h3>Quick Matching</h3>
                    <p>Advanced algorithms quickly match blood requests with available donors.</p>
                </div>
                <div class="about-card animate__animated animate__fadeInRight">
                    <div class="about-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3>Real-time Tracking</h3>
                    <p>Monitor blood inventory levels and requests in real-time.</p>
                </div>
            </div>
        </section>
    </main>

    <footer class="animate__animated animate__fadeIn animate__delay-2s">
        <div class="container">
            <p>&copy; Blood Donation System. Graduation Project</p>
            <div class="social-links">
                <a href="#"><i class="fab fa-facebook"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
            </div>
        </div>
    </footer>

    <div class="floating-buttons">
        <button class="floating-btn pulse" id="emergency-btn">
            <i class="fas fa-ambulance"></i>
            <span class="tooltip">Emergency Request</span>
        </button>
        <button class="floating-btn" id="chat-btn">
            <i class="fas fa-comment-medical"></i>
            <span class="tooltip">Live Chat</span>
        </button>
    </div>

    <script src="assets/js/main.js"></script>
</body>
</html>

