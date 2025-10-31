<?php

include_once('database/db.php');

include_once('functions/functions.php');
                     
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Waste Wizard | Smart Waste Management Solutions</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4CAF50;
            --primary-dark: #388E3C;
            --primary-light: #C8E6C9;
            --secondary: #FFC107;
            --dark: #212121;
            --light: #FAFAFA;
            --gray: #E0E0E0;
            --text: #424242;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
        }
        
        body {
            line-height: 1.6;
            color: var(--text);
            background-color: var(--light);
            overflow-x: hidden;
        }
        
        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        /* Header Styles */
        header {
            background-color: white;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
            position: sticky;
            top: 0;
            z-index: 100;
            padding: 10px 0;
        }
        
        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }
        
        .logo-icon {
            background-color: var(--primary);
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            transition: transform 0.3s ease;
        }
        
        .logo:hover .logo-icon {
            transform: rotate(15deg);
        }
        
        .logo-text {
            font-weight: 700;
            color: var(--dark);
            font-size: 24px;
        }
        
        .logo-text span {
            color: var(--primary);
        }
        
        .nav-links {
            display: flex;
            gap: 25px;
        }
        
        .nav-links a {
            text-decoration: none;
            color: var(--dark);
            font-weight: 500;
            font-size: 16px;
            position: relative;
            padding: 5px 0;
            transition: color 0.3s;
        }
        
        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background-color: var(--primary);
            transition: width 0.3s;
        }
        
        .nav-links a:hover {
            color: var(--primary);
        }
        
        .nav-links a:hover::after {
            width: 100%;
        }
        
        .auth-buttons {
            display: flex;
            gap: 15px;
        }
        
        .btn {
            padding: 10px 22px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            font-size: 15px;
            border: none;
        }
        
        .btn-outline {
            border: 2px solid var(--primary);
            color: var(--primary);
            background: transparent;
        }
        
        .btn-outline:hover {
            background-color: var(--primary);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(76, 175, 80, 0.2);
        }
        
        .btn-solid {
            background-color: var(--primary);
            color: white;
            border: 2px solid var(--primary);
        }
        
        .btn-solid:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(76, 175, 80, 0.3);
        }
        
        /* Hero Section */
        .hero {
            padding: 90px 0;
            background: linear-gradient(135deg, rgba(76, 175, 80, 0.1) 0%, rgba(255, 255, 255, 1) 100%);
        }
        
        .hero-content {
            display: flex;
            align-items: center;
            gap: 50px;
        }
        
        .hero-text {
            flex: 1;
        }
        
        .hero-text h2 {
            font-size: 42px;
            margin-bottom: 20px;
            line-height: 1.2;
            color: var(--dark);
        }
        
        .hero-text h2 span {
            color: var(--primary);
        }
        
        .hero-text p {
            font-size: 18px;
            margin-bottom: 30px;
            color: #555;
            max-width: 90%;
        }
        
        .hero-buttons {
            display: flex;
            gap: 20px;
        }
        
        .hero-image {
            flex: 1;
            position: relative;
        }
        
        .hero-image img {
            width: 100%;
            border-radius: 10px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
            transition: transform 0.5s;
        }
        
        .hero-image:hover img {
            transform: scale(1.02);
        }
        
        /* Features Section */
        .features {
            padding: 80px 0;
            background-color: white;
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 50px;
        }
        
        .section-title h3 {
            font-size: 32px;
            color: var(--dark);
        }
        
        .section-title p {
            color: #777;
            max-width: 700px;
            margin: 10px auto 0;
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }
        
        .feature-card {
            background-color: var(--light);
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.08);
        }
        
        .feature-icon {
            width: 70px;
            height: 70px;
            background-color: rgba(76, 175, 80, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 30px;
            color: var(--primary);
            transition: all 0.3s;
        }
        
        .feature-card:hover .feature-icon {
            background-color: var(--primary);
            color: white;
        }
        
        .feature-card h4 {
            font-size: 20px;
            margin-bottom: 15px;
            color: var(--dark);
        }
        
        .feature-card p {
            color: #666;
        }
        
        /* How It Works Section */
        .how-it-works {
            padding: 80px 0;
            background-color: var(--primary-light);
        }
        
        .steps {
            display: flex;
            justify-content: space-between;
            margin-top: 50px;
        }
        
        .step {
            flex: 1;
            text-align: center;
            padding: 0 15px;
            position: relative;
        }
        
        .step:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 40px;
            right: 0;
            width: 100%;
            height: 2px;
            background-color: var(--primary);
            opacity: 0.3;
        }
        
        .step-number {
            width: 50px;
            height: 50px;
            background-color: var(--primary);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: 700;
            margin: 0 auto 20px;
        }
        
        .step h4 {
            font-size: 18px;
            margin-bottom: 15px;
            color: var(--dark);
        }
        
        .step p {
            color: var(--text);
            font-size: 15px;
        }
        
        /* CTA Section */
        .cta {
            padding: 80px 0;
            background-color: var(--primary);
            color: white;
            text-align: center;
        }
        
        .cta h3 {
            font-size: 32px;
            margin-bottom: 20px;
        }
        
        .cta p {
            max-width: 700px;
            margin: 0 auto 30px;
            font-size: 18px;
            opacity: 0.9;
        }
        
        .cta-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
        }
        
        .btn-light {
            background-color: white;
            color: var(--primary);
            border: 2px solid white;
        }
        
        .btn-light:hover {
            background-color: transparent;
            color: white;
            transform: translateY(-2px);
        }
        
        .btn-dark-outline {
            border: 2px solid white;
            color: white;
            background: transparent;
        }
        
        .btn-dark-outline:hover {
            background-color: white;
            color: var(--primary);
            transform: translateY(-2px);
        }
        
        /* Footer */
        footer {
            background-color: var(--dark);
            color: white;
            padding: 60px 0 20px;
        }
        
        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 40px;
            margin-bottom: 40px;
        }
        
        .footer-column h4 {
            font-size: 18px;
            margin-bottom: 20px;
            position: relative;
            padding-bottom: 10px;
        }
        
        .footer-column h4::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 40px;
            height: 2px;
            background-color: var(--primary);
        }
        
        .footer-column p {
            color: #bbb;
            margin-bottom: 20px;
        }
        
        .footer-column ul {
            list-style: none;
        }
        
        .footer-column ul li {
            margin-bottom: 12px;
        }
        
        .footer-column ul li a {
            color: #bbb;
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .footer-column ul li a:hover {
            color: white;
        }
        
        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }
        
        .social-links a {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.3s;
        }
        
        .social-links a:hover {
            background-color: var(--primary);
        }
        
        .contact-info {
            margin-top: 20px;
        }
        
        .contact-item {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 15px;
            color: #bbb;
        }
        
        .contact-item i {
            color: var(--primary);
            margin-top: 3px;
        }
        
        .footer-bottom {
            text-align: center;
            padding-top: 30px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: #bbb;
            font-size: 14px;
        }
        
        /* Responsive Styles */
        @media (max-width: 992px) {
            .hero-content {
                gap: 40px;
            }
            
            .hero-text h2 {
                font-size: 36px;
            }
            
            .steps {
                flex-wrap: wrap;
                gap: 30px;
            }
            
            .step {
                flex: 0 0 calc(50% - 30px);
                margin-bottom: 30px;
            }
            
            .step:not(:last-child)::after {
                display: none;
            }
        }
        
        @media (max-width: 768px) {
            .header-container {
                flex-direction: column;
                gap: 20px;
                padding-bottom: 20px;
            }
            
            .nav-links {
                gap: 15px;
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .hero-content {
                flex-direction: column;
                text-align: center;
            }
            
            .hero-text p {
                max-width: 100%;
                margin-left: auto;
                margin-right: auto;
            }
            
            .hero-buttons {
                justify-content: center;
            }
            
            .cta-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .btn {
                width: 100%;
                max-width: 250px;
                text-align: center;
            }
        }
        
        @media (max-width: 576px) {
            .hero-text h2 {
                font-size: 30px;
            }
            
            .hero-buttons {
                flex-direction: column;
                gap: 15px;
            }
            
            .section-title h3 {
                font-size: 28px;
            }
            
            .step {
                flex: 0 0 100%;
            }
            
            .footer-content {
                grid-template-columns: 1fr;
                gap: 30px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
     
    <?php include("navbar.php");?>
    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <div class="hero-text">
                    <h2>Smart Waste Management <span>Made Simple</span></h2>
                    <p>Waste Wizard helps individuals and businesses track, reduce, and properly dispose of waste with our intuitive platform. Join our community of eco-conscious users today.</p>
                    <div class="hero-buttons">
                        <a href="how-it-works" class="btn btn-solid">Get Started</a>
                        <a href="features" class="btn btn-outline">Learn More</a>
                    </div>
                </div>
                <div class="hero-image">
                    <img src="https://images.unsplash.com/photo-1600585152220-90363fe7e115?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80" alt="Waste management dashboard">
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features">
        <div class="container">
            <div class="section-title">
                <h3>Why Choose Waste Wizard?</h3>
                <p>Our platform offers comprehensive solutions to make waste management effortless and effective</p>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h4>Smart Analytics</h4>
                    <p>Track your waste generation patterns with detailed reports and visualizations to optimize your disposal habits.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <h4>Collection Scheduling</h4>
                    <p>Never miss a pickup with our smart scheduling system that adapts to your needs and local collection times.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <h4>Rewards Program</h4>
                    <p>Earn points for proper waste disposal that can be redeemed for discounts and eco-friendly products.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="how-it-works">
        <div class="container">
            <div class="section-title">
                <h3>How Waste Wizard Works</h3>
                <p>Getting started with our waste management solution is quick and easy</p>
            </div>
            <div class="steps">
                <div class="step">
                    <div class="step-number">1</div>
                    <h4>Sign Up</h4>
                    <p>Create your account in minutes with our simple registration process.</p>
                </div>
                <div class="step">
                    <div class="step-number">2</div>
                    <h4>Set Up Your Profile</h4>
                    <p>Tell us about your waste habits and disposal needs.</p>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
                    <h4>Connect Services</h4>
                    <p>Link your local waste collection services or schedule private pickups.</p>
                </div>
                <div class="step">
                    <div class="step-number">4</div>
                    <h4>Start Tracking</h4>
                    <p>Log your waste and recycling to see your environmental impact.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta">
        <div class="container">
            <h3>Ready to Transform Your Waste Habits?</h3>
            <p>Join thousands of users who are making a difference for the planet while saving time and money.</p>
            <div class="cta-buttons">
                <a href="viewplans" class="btn btn-light">View Plans</a>
                <a href="contact" class="btn btn-dark-outline">Contact Us</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include("footer.php");?>
</body>
</html>