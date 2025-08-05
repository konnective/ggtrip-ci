<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #e91e63;
            --secondary-color: #1c2541;
        }

        body {
            background: linear-gradient(135deg, var(--secondary-color) 0%, #0f172a 100%);
            color: white;
            font-family: 'Arial', sans-serif;
            overflow-x: hidden;
        }

        .error-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
        }

        .floating-shapes {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: -1;
        }

        .shape {
            position: absolute;
            opacity: 0.1;
            animation: float 6s ease-in-out infinite;
        }

        .shape-1 {
            top: 20%;
            left: 10%;
            font-size: 3rem;
            color: var(--primary-color);
            animation-delay: 0s;
        }

        .shape-2 {
            top: 60%;
            right: 15%;
            font-size: 2rem;
            color: var(--primary-color);
            animation-delay: 2s;
        }

        .shape-3 {
            bottom: 30%;
            left: 20%;
            font-size: 2.5rem;
            color: var(--primary-color);
            animation-delay: 4s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(10deg); }
        }

        .error-404 {
            font-size: clamp(4rem, 15vw, 12rem);
            font-weight: 900;
            background: linear-gradient(45deg, var(--primary-color), #f06292);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: 0 0 30px rgba(233, 30, 99, 0.5);
            animation: glow 2s ease-in-out infinite alternate;
            line-height: 1;
            margin-bottom: 1rem;
        }

        @keyframes glow {
            from { filter: drop-shadow(0 0 10px rgba(233, 30, 99, 0.3)); }
            to { filter: drop-shadow(0 0 20px rgba(233, 30, 99, 0.6)); }
        }

        .error-title {
            color: var(--primary-color);
            font-weight: 700;
            margin-bottom: 1.5rem;
            font-size: clamp(1.5rem, 4vw, 2.5rem);
        }

        .error-description {
            font-size: 1.1rem;
            color: #cbd5e1;
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .btn-home {
            background: linear-gradient(45deg, var(--primary-color), #ad1457);
            border: none;
            padding: 1rem 2rem;
            font-weight: 600;
            border-radius: 50px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 4px 15px rgba(233, 30, 99, 0.3);
        }

        .btn-home:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(233, 30, 99, 0.4);
            background: linear-gradient(45deg, #ad1457, var(--primary-color));
        }

        .btn-search {
            background: transparent;
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            padding: 1rem 2rem;
            font-weight: 600;
            border-radius: 50px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-left: 1rem;
        }

        .btn-search:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(233, 30, 99, 0.3);
        }

        .error-illustration {
            text-align: center;
            margin-bottom: 2rem;
        }

        .broken-link {
            font-size: 5rem;
            color: var(--primary-color);
            animation: shake 3s ease-in-out infinite;
            margin-bottom: 1rem;
        }

        @keyframes shake {
            0%, 100% { transform: rotate(0deg); }
            25% { transform: rotate(-5deg); }
            75% { transform: rotate(5deg); }
        }

        .search-suggestions {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 15px;
            padding: 2rem;
            margin-top: 3rem;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(233, 30, 99, 0.2);
        }

        .suggestion-item {
            display: flex;
            align-items: center;
            padding: 0.8rem 0;
            color: #cbd5e1;
            text-decoration: none;
            transition: all 0.3s ease;
            border-radius: 8px;
            margin: 0.5rem 0;
            padding-left: 1rem;
        }

        .suggestion-item:hover {
            color: var(--primary-color);
            background: rgba(233, 30, 99, 0.1);
            transform: translateX(10px);
        }

        .suggestion-item i {
            margin-right: 1rem;
            color: var(--primary-color);
        }

        @media (max-width: 768px) {
            .btn-search {
                margin-left: 0;
                margin-top: 1rem;
            }
            
            .error-description {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Floating Background Shapes -->
    <div class="floating-shapes">
        <div class="shape shape-1">
            <i class="fas fa-code"></i>
        </div>
        <div class="shape shape-2">
            <i class="fas fa-bug"></i>
        </div>
        <div class="shape shape-3">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
    </div>

    <div class="error-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <!-- Error Illustration -->
                    <div class="error-illustration">
                        <div class="broken-link">
                            <i class="fas fa-unlink"></i>
                        </div>
                    </div>

                    <!-- 404 Number -->
                    <h1 class="error-404">404</h1>
                    
                    <!-- Title -->
                    <h2 class="error-title">Oops! Page Not Found</h2>
                    
                    <!-- Description -->
                    <p class="error-description">
                        The page you're looking for seems to have wandered off into the digital void. 
                        Don't worry though, even the best explorers sometimes take a wrong turn!
                    </p>
                    
                    <!-- Action Buttons -->
                    <div class="d-flex flex-wrap justify-content-center align-items-center">
                        <a href="#" class="btn btn-primary btn-home" onclick="goHome()">
                            <i class="fas fa-home"></i>
                            Go Home
                        </a>
                    </div>
                    
                    <!-- Search Suggestions -->
                    <!-- <div class="search-suggestions">
                        <h5 class="mb-3" style="color: var(--primary-color);">
                            <i class="fas fa-lightbulb me-2"></i>
                            Maybe you were looking for:
                        </h5>
                        <div class="row">
                            <div class="col-md-6">
                                <a href="#" class="suggestion-item">
                                    <i class="fas fa-home"></i>
                                    Homepage
                                </a>
                                <a href="#" class="suggestion-item">
                                    <i class="fas fa-user"></i>
                                    About Us
                                </a>
                                <a href="#" class="suggestion-item">
                                    <i class="fas fa-envelope"></i>
                                    Contact
                                </a>
                            </div>
                            <div class="col-md-6">
                                <a href="#" class="suggestion-item">
                                    <i class="fas fa-blog"></i>
                                    Blog
                                </a>
                                <a href="#" class="suggestion-item">
                                    <i class="fas fa-shopping-cart"></i>
                                    Products
                                </a>
                                <a href="#" class="suggestion-item">
                                    <i class="fas fa-question-circle"></i>
                                    Help Center
                                </a>
                            </div>
                        </div>
                    </div> -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Go Home function
        function goHome() {
            // Add a smooth transition effect
            document.body.style.opacity = '0';
            document.body.style.transition = 'opacity 0.5s ease';
            
            setTimeout(() => {
                // Replace with your actual home URL
                window.location.href = '/flight-booking';
            }, 500);
        }

        // Search function
        function performSearch() {
            const searchTerm = prompt('What are you looking for?');
            if (searchTerm && searchTerm.trim()) {
                // Replace with your actual search URL
                window.location.href = `/search?q=${encodeURIComponent(searchTerm)}`;
            }
        }

        // Add click handlers to suggestion items
        document.querySelectorAll('.suggestion-item').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Add ripple effect
                const ripple = document.createElement('span');
                ripple.style.position = 'absolute';
                ripple.style.borderRadius = '50%';
                ripple.style.background = 'rgba(233, 30, 99, 0.6)';
                ripple.style.transform = 'scale(0)';
                ripple.style.animation = 'ripple 0.6s linear';
                ripple.style.left = e.offsetX + 'px';
                ripple.style.top = e.offsetY + 'px';
                ripple.style.width = ripple.style.height = '20px';
                
                this.style.position = 'relative';
                this.appendChild(ripple);
                
                setTimeout(() => {
                    ripple.remove();
                }, 600);
            });
        });

        // Add ripple animation to CSS
        const style = document.createElement('style');
        style.textContent = `
            @keyframes ripple {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);

        // Add some interactive particles
        function createParticle() {
            const particle = document.createElement('div');
            particle.style.position = 'fixed';
            particle.style.width = '4px';
            particle.style.height = '4px';
            particle.style.background = '#e91e63';
            particle.style.borderRadius = '50%';
            particle.style.pointerEvents = 'none';
            particle.style.opacity = '0.7';
            particle.style.left = Math.random() * window.innerWidth + 'px';
            particle.style.top = window.innerHeight + 'px';
            particle.style.zIndex = '-1';
            
            document.body.appendChild(particle);
            
            const animation = particle.animate([
                { transform: 'translateY(0px)', opacity: 0.7 },
                { transform: `translateY(-${window.innerHeight + 100}px)`, opacity: 0 }
            ], {
                duration: Math.random() * 3000 + 2000,
                easing: 'linear'
            });
            
            animation.onfinish = () => particle.remove();
        }

        // Create particles periodically
        setInterval(createParticle, 300);
    </script>
</body>
</html>