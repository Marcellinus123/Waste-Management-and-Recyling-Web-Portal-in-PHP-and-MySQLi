<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Learn More | Waste Wizard - Smart Waste Management</title>
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
            transition: transform 0.3s;
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
        }
        
        .btn-solid {
            background-color: var(--primary);
            color: white;
            border: 2px solid var(--primary);
        }
        
        .btn-solid:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
        }
        
        /* Page Header */
        .page-header {
            padding: 100px 0 60px;
            background: linear-gradient(135deg, rgba(76, 175, 80, 0.1) 0%, rgba(255, 255, 255, 1) 100%);
            text-align: center;
        }
        
        .page-header h1 {
            font-size: 42px;
            color: var(--dark);
            margin-bottom: 15px;
        }
        
        .page-header p {
            font-size: 18px;
            color: #555;
            max-width: 700px;
            margin: 0 auto;
        }
        
        /* Learn More Sections */
        .learn-section {
            padding: 80px 0;
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 50px;
        }
        
        .section-title h2 {
            font-size: 32px;
            color: var(--dark);
            margin-bottom: 15px;
        }
        
        .section-title p {
            color: #555;
            max-width: 700px;
            margin: 0 auto;
        }
        
        /* How It Works */
        .how-it-works {
            background-color: white;
        }
        
        .steps {
            display: flex;
            justify-content: space-between;
            margin-top: 50px;
            position: relative;
        }
        
        .steps::before {
            content: '';
            position: absolute;
            top: 40px;
            left: 0;
            right: 0;
            height: 3px;
            background-color: var(--gray);
            z-index: 1;
        }
        
        .step {
            flex: 1;
            text-align: center;
            position: relative;
            z-index: 2;
            max-width: 200px;
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
            border: 5px solid white;
        }
        
        .step h4 {
            font-size: 18px;
            margin-bottom: 15px;
            color: var(--dark);
        }
        
        .step p {
            color: #666;
            font-size: 15px;
        }
        
        /* Benefits Section */
        .benefits-section {
            background-color: var(--primary-light);
        }
        
        .benefits-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }
        
        .benefit-card {
            background-color: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s;
        }
        
        .benefit-card:hover {
            transform: translateY(-10px);
        }
        
        .benefit-icon {
            font-size: 40px;
            color: var(--primary);
            margin-bottom: 20px;
        }
        
        .benefit-card h3 {
            font-size: 20px;
            margin-bottom: 15px;
            color: var(--dark);
        }
        
        .benefit-card p {
            color: #555;
        }
        
        /* Testimonials */
        .testimonials-section {
            background-color: white;
        }
        
        .testimonial-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }
        
        .testimonial-card {
            background-color: var(--light);
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
        
        .testimonial-text {
            font-style: italic;
            color: #555;
            margin-bottom: 20px;
            position: relative;
        }
        
        .testimonial-text::before {
            content: '"';
            font-size: 60px;
            color: var(--primary-light);
            position: absolute;
            top: -20px;
            left: -15px;
            line-height: 1;
            opacity: 0.3;
        }
        
        .testimonial-author {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .author-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }
        
        .author-info h5 {
            font-size: 16px;
            margin-bottom: 5px;
            color: var(--dark);
        }
        
        .author-info p {
            font-size: 14px;
            color: #777;
        }
        
        /* CTA Section */
        .cta-section {
            padding: 100px 0;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            text-align: center;
        }
        
        .cta-section h2 {
            font-size: 32px;
            margin-bottom: 20px;
        }
        
        .cta-section p {
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
        }
        
        .btn-dark-outline {
            border: 2px solid white;
            color: white;
            background: transparent;
        }
        
        .btn-dark-outline:hover {
            background-color: white;
            color: var(--primary);
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
        
        .footer-bottom {
            text-align: center;
            padding-top: 30px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: #bbb;
            font-size: 14px;
        }
        
        /* Responsive Styles */
        @media (max-width: 992px) {
            .steps {
                flex-wrap: wrap;
                gap: 30px;
            }
            
            .steps::before {
                display: none;
            }
            
            .step {
                flex: 0 0 calc(50% - 15px);
                margin-bottom: 30px;
            }
            
            .page-header h1 {
                font-size: 36px;
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
            
            .auth-buttons {
                width: 100%;
                justify-content: center;
            }
            
            .page-header h1 {
                font-size: 32px;
            }
            
            .cta-buttons {
                flex-direction: column;
                align-items: center;
            }
        }
        
        @media (max-width: 576px) {
            .page-header {
                padding: 80px 0 40px;
            }
            
            .page-header h1 {
                font-size: 28px;
            }
            
            .step {
                flex: 0 0 100%;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
     <?php include("navbar.php");?>

    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <h1>Learn More About Waste Wizard</h1>
            <p>Discover how our smart waste management solution can transform your habits and help create a cleaner, greener future</p>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="learn-section how-it-works">
        <div class="container">
            <div class="section-title">
                <h2>How Waste Wizard Works</h2>
                <p>Our platform makes sustainable waste management simple and rewarding</p>
            </div>
            
            <div class="steps">
                <div class="step">
                    <div class="step-number">1</div>
                    <h4>Sign Up</h4>
                    <p>Create your free account in minutes with just your email address</p>
                </div>
                <div class="step">
                    <div class="step-number">2</div>
                    <h4>Set Up Your Profile</h4>
                    <p>Tell us about your location, waste habits, and sustainability goals</p>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
                    <h4>Connect Services</h4>
                    <p>Link your local waste collection services or schedule private pickups</p>
                </div>
                <div class="step">
                    <div class="step-number">4</div>
                    <h4>Start Tracking</h4>
                    <p>Log your waste and recycling to see your environmental impact</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Benefits Section -->
    <section class="learn-section benefits-section">
        <div class="container">
            <div class="section-title">
                <h2>Why Choose Waste Wizard?</h2>
                <p>The benefits of smart waste management for you and your community</p>
            </div>
            
            <div class="benefits-grid">
                <div class="benefit-card">
                    <div class="benefit-icon">
                        <i class="fas fa-leaf"></i>
                    </div>
                    <h3>Reduce Environmental Impact</h3>
                    <p>Lower your carbon footprint by optimizing waste disposal and increasing recycling rates with our data-driven recommendations.</p>
                </div>
                
                <div class="benefit-card">
                    <div class="benefit-icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <h3>Save Money</h3>
                    <p>Reduce waste collection costs by up to 30% through optimized scheduling and reduced contamination of recycling streams.</p>
                </div>
                
                <div class="benefit-card">
                    <div class="benefit-icon">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <h3>Earn Rewards</h3>
                    <p>Get points for proper waste disposal that can be redeemed for discounts at eco-friendly businesses and products.</p>
                </div>
                
                <div class="benefit-card">
                    <div class="benefit-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3>Track Progress</h3>
                    <p>Visual dashboards show your waste reduction achievements and help you set new sustainability goals.</p>
                </div>
                
                <div class="benefit-card">
                    <div class="benefit-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>Community Impact</h3>
                    <p>Join a network of environmentally conscious users making a collective difference in waste reduction.</p>
                </div>
                
                <div class="benefit-card">
                    <div class="benefit-icon">
                        <i class="fas fa-award"></i>
                    </div>
                    <h3>Certification</h3>
                    <p>Earn verifiable sustainability certifications for your home or business to showcase your environmental commitment.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="learn-section testimonials-section">
        <div class="container">
            <div class="section-title">
                <h2>What Our Users Say</h2>
                <p>Hear from people who transformed their waste management with Waste Wizard</p>
            </div>
            
            <div class="testimonial-grid">
                <div class="testimonial-card">
                    <div class="testimonial-text">
                        Waste Wizard helped our office reduce landfill waste by 60% in just three months. The analytics dashboard showed us exactly where we could improve our recycling efforts.
                    </div>
                    <div class="testimonial-author">
                        <img src="https://randomuser.me/api/portraits/women/45.jpg" alt="Sarah Johnson" class="author-avatar">
                        <div class="author-info">
                            <h5>Sarah Johnson</h5>
                            <p>Office Manager, GreenTech Solutions</p>
                        </div>
                    </div>
                </div>
                
                <div class="testimonial-card">
                    <div class="testimonial-text">
                        As a busy parent, I love how Waste Wizard reminds me about collection days and helps me teach my kids about proper recycling through the fun rewards system.
                    </div>
                    <div class="testimonial-author">
                        <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Michael Chen" class="author-avatar">
                        <div class="author-info">
                            <h5>Michael Chen</h5>
                            <p>Home User, Seattle</p>
                        </div>
                    </div>
                </div>
                
                <div class="testimonial-card">
                    <div class="testimonial-text">
                        The waste audit tools helped us identify reduction opportunities that saved our restaurant over $8,000 annually in disposal costs. Worth every penny!
                    </div>
                    <div class="testimonial-author">
                        <img src="https://randomuser.me/api/portraits/women/68.jpg" alt="Elena Rodriguez" class="author-avatar">
                        <div class="author-info">
                            <h5>Elena Rodriguez</h5>
                            <p>Owner, Fresh Bites Café</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <h2>Ready to Transform Your Waste Habits?</h2>
            <p>Join thousands of satisfied users who are making a difference for the planet while saving time and money.</p>
            <div class="cta-buttons">
                <a href="pricing.html" class="btn btn-light">View Plans</a>
                <a href="contact.html" class="btn btn-dark-outline">Contact Us</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include("footer.php");?>
</body>
</html>