document.addEventListener('DOMContentLoaded', function() {
    // Remove preload class after page loads
    setTimeout(function() {
        document.body.classList.remove('preload');
    }, 500);

    // Navigation handling
    const navLinks = document.querySelectorAll('.nav-link');
    const contentSections = document.querySelectorAll('.content-section');

    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Remove active class from all links
            navLinks.forEach(navLink => navLink.classList.remove('active'));
            
            // Add active class to clicked link
            this.classList.add('active');
            
            // Hide all content sections
            contentSections.forEach(section => section.classList.remove('active'));
            
            // Show the selected content section
            const sectionId = this.getAttribute('data-section');
            document.getElementById(sectionId).classList.add('active');
            
            // Add animation to the new section
            document.getElementById(sectionId).classList.add('animate__animated', 'animate__fadeIn');
        });
    });

    // Floating button actions
    const emergencyBtn = document.getElementById('emergency-btn');
    const chatBtn = document.getElementById('chat-btn');

    emergencyBtn.addEventListener('click', function() {
        window.location.href = 'register.php?role=requester&emergency=1';
    });

    chatBtn.addEventListener('click', function() {
        alert('Live chat feature coming soon!');
    });

    // Animate stats on scroll
    const statsContainer = document.querySelector('.stats-container');
    const statValues = document.querySelectorAll('.stat-value');

    function animateStats() {
        statValues.forEach((value, index) => {
            const target = parseInt(value.textContent.replace('+', ''));
            const increment = target / 100;
            let current = 0;
            
            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    clearInterval(timer);
                    value.textContent = target.toLocaleString() + '+';
                } else {
                    value.textContent = Math.floor(current).toLocaleString();
                }
            }, 20);
        });
    }

    // Intersection Observer for scroll animations
    const observerOptions = {
        threshold: 0.1
    };

    const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate__animated', 'animate__fadeInUp');
                observer.unobserve(entry.target);
                
                if (entry.target.classList.contains('stats-container')) {
                    animateStats();
                }
            }
        });
    }, observerOptions);

    // Observe elements that should animate on scroll
    const animateOnScroll = document.querySelectorAll('.stats-container, .about-card');
    animateOnScroll.forEach(element => {
        observer.observe(element);
    });

    // Blood drop animation for emergency button
    emergencyBtn.addEventListener('mouseenter', function() {
        createBloodDrops(3);
    });

    function createBloodDrops(count) {
        for (let i = 0; i < count; i++) {
            const drop = document.createElement('div');
            drop.classList.add('floating-blood-drop');
            drop.style.left = Math.random() * 100 + 'px';
            drop.style.animationDuration = (Math.random() * 2 + 1) + 's';
            emergencyBtn.appendChild(drop);
            
            setTimeout(() => {
                drop.remove();
            }, 2000);
        }
    }

    // Dynamic blood type coloring
    const bloodTypeElements = document.querySelectorAll('.blood-type');
    bloodTypeElements.forEach(element => {
        const bloodType = element.textContent.trim();
        element.classList.add('blood-type-' + bloodType.toLowerCase().replace('+', 'plus').replace('-', 'minus'));
    });

    // Add hover effect to cards
    const cards = document.querySelectorAll('.stat-card, .about-card');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
});

// Additional styles for floating blood drops
const style = document.createElement('style');
style.textContent = `
    .floating-blood-drop {
        position: absolute;
        width: 10px;
        height: 10px;
        background-color: var(--danger);
        border-radius: 50%;
        opacity: 0.7;
        animation: falling-drop 2s linear forwards;
        pointer-events: none;
    }
    
    @keyframes falling-drop {
        0% {
            transform: translateY(0) translateX(0);
            opacity: 0.7;
        }
        100% {
            transform: translateY(100px) translateX(20px);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);
