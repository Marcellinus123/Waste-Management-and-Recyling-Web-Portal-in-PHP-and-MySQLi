<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ | Waste Wizard - Smart Waste Management</title>
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
        
        /* Search Bar */
        .search-section {
            padding: 0 0 40px;
            max-width: 800px;
            margin: 0 auto;
        }
        
        .search-bar {
            position: relative;
        }
        
        .search-bar input {
            width: 100%;
            padding: 15px 20px 15px 50px;
            border: 1px solid var(--gray);
            border-radius: 50px;
            font-size: 16px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
        
        .search-bar i {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: #777;
        }
        
        /* FAQ Categories */
        .categories-section {
            padding: 40px 0;
        }
        
        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            max-width: 800px;
            margin: 0 auto;
        }
        
        .category-card {
            background-color: white;
            border-radius: 10px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
            border: 2px solid transparent;
        }
        
        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            border-color: var(--primary-light);
        }
        
        .category-card.active {
            background-color: var(--primary);
            color: white;
            border-color: var(--primary);
        }
        
        .category-card i {
            font-size: 30px;
            margin-bottom: 15px;
            color: var(--primary);
        }
        
        .category-card.active i {
            color: white;
        }
        
        .category-card h3 {
            font-size: 18px;
        }
        
        /* FAQ Section */
        .faq-section {
            padding: 40px 0 80px;
        }
        
        .faq-container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .faq-category-title {
            font-size: 28px;
            margin-bottom: 30px;
            color: var(--dark);
            padding-bottom: 10px;
            border-bottom: 2px solid var(--primary-light);
        }
        
        .faq-item {
            margin-bottom: 15px;
            border: 1px solid var(--gray);
            border-radius: 8px;
            overflow: hidden;
            transition: all 0.3s;
        }
        
        .faq-item.active {
            border-color: var(--primary);
            box-shadow: 0 5px 15px rgba(76, 175, 80, 0.1);
        }
        
        .faq-question {
            padding: 20px;
            background-color: white;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .faq-question:hover {
            background-color: #f9f9f9;
        }
        
        .faq-question i {
            color: var(--primary);
            transition: transform 0.3s;
        }
        
        .faq-item.active .faq-question i {
            transform: rotate(180deg);
        }
        
        .faq-answer {
            padding: 0 20px;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s, padding 0.3s;
            background-color: white;
        }
        
        .faq-item.active .faq-answer {
            padding: 0 20px 20px;
            max-height: 500px;
        }
        
        .faq-answer p {
            margin-bottom: 15px;
        }
        
        .faq-answer ul {
            margin-left: 20px;
            margin-bottom: 15px;
        }
        
        /* Contact CTA */
        .contact-cta {
            padding: 80px 0;
            background-color: var(--primary-light);
            text-align: center;
        }
        
        .contact-cta h2 {
            font-size: 32px;
            margin-bottom: 20px;
            color: var(--dark);
        }
        
        .contact-cta p {
            max-width: 700px;
            margin: 0 auto 30px;
            color: #555;
            font-size: 18px;
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
            
            .categories-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (max-width: 576px) {
            .page-header {
                padding: 80px 0 40px;
            }
            
            .page-header h1 {
                font-size: 28px;
            }
            
            .categories-grid {
                grid-template-columns: 1fr;
            }
            
            .faq-category-title {
                font-size: 24px;
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
            <h1>Frequently Asked Questions</h1>
            <p>Find answers to common questions about Waste Wizard and our waste management solutions</p>
        </div>
    </section>

    <!-- Search Bar -->
    <section class="search-section">
        <div class="container">
            <div class="search-bar">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search our FAQs...">
            </div>
        </div>
    </section>

    <!-- FAQ Categories -->
    <section class="categories-section">
        <div class="container">
            <div class="categories-grid">
                <div class="category-card active" data-category="general">
                    <i class="fas fa-question-circle"></i>
                    <h3>General</h3>
                </div>
                <div class="category-card" data-category="account">
                    <i class="fas fa-user-circle"></i>
                    <h3>Account</h3>
                </div>
                <div class="category-card" data-category="features">
                    <i class="fas fa-cogs"></i>
                    <h3>Features</h3>
                </div>
                <div class="category-card" data-category="billing">
                    <i class="fas fa-credit-card"></i>
                    <h3>Billing</h3>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq-section">
        <div class="container">
            <div class="faq-container">
                <!-- General FAQs -->
                <div class="faq-category" id="general-category">
                    <h2 class="faq-category-title">General Questions</h2>
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            <span>What is Waste Wizard?</span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Waste Wizard is a smart waste management platform that helps individuals and businesses track, reduce, and properly dispose of their waste. Our app provides tools for waste analytics, collection scheduling, recycling rewards, and more to make sustainable waste management simple and rewarding.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            <span>How does Waste Wizard help the environment?</span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Waste Wizard contributes to environmental sustainability by:</p>
                            <ul>
                                <li>Reducing landfill waste through better tracking and recycling incentives</li>
                                <li>Optimizing collection routes to lower carbon emissions</li>
                                <li>Educating users about proper waste disposal</li>
                                <li>Providing data to help communities improve their waste systems</li>
                                <li>Rewarding sustainable behavior through our recycling rewards program</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            <span>Is my data secure with Waste Wizard?</span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Yes, we take data security very seriously. We use industry-standard encryption for all data transmission and storage. Our privacy policy outlines exactly what data we collect and how we use it. We never sell your personal information to third parties.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            <span>What devices support the Waste Wizard app?</span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Waste Wizard is available on:</p>
                            <ul>
                                <li>iOS devices (iPhone, iPad) running iOS 13 or later</li>
                                <li>Android devices running Android 8.0 or later</li>
                                <li>Any modern web browser via our web app</li>
                            </ul>
                            <p>We're constantly working to expand our platform support.</p>
                        </div>
                    </div>
                </div>
                
                <!-- Account FAQs -->
                <div class="faq-category" id="account-category" style="display: none;">
                    <h2 class="faq-category-title">Account Questions</h2>
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            <span>How do I create an account?</span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Creating an account is easy:</p>
                            <ol>
                                <li>Click "Sign Up" in the top right corner of our website or app</li>
                                <li>Enter your email address and create a password</li>
                                <li>Verify your email by clicking the link we send you</li>
                                <li>Complete your profile setup</li>
                                <li>Start using Waste Wizard!</li>
                            </ol>
                        </div>
                    </div>
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            <span>I forgot my password. How can I reset it?</span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>To reset your password:</p>
                            <ol>
                                <li>Go to the login page</li>
                                <li>Click "Forgot Password"</li>
                                <li>Enter the email address associated with your account</li>
                                <li>Check your email for a password reset link</li>
                                <li>Click the link and follow the instructions to create a new password</li>
                            </ol>
                            <p>If you don't receive the email, please check your spam folder or contact our support team.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            <span>Can I use Waste Wizard without creating an account?</span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>You can access some basic features like our recycling facility locator without an account, but to get the full Waste Wizard experience including waste tracking, collection scheduling, and rewards, you'll need to create a free account.</p>
                            <p>Our Basic account is completely free with no credit card required.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            <span>How do I delete my account?</span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>We're sorry to see you go! To delete your account:</p>
                            <ol>
                                <li>Log in to your account</li>
                                <li>Go to Account Settings</li>
                                <li>Click "Delete Account" at the bottom</li>
                                <li>Confirm your decision</li>
                            </ol>
                            <p>Note: Account deletion is permanent and cannot be undone. All your data will be removed from our systems in accordance with our privacy policy.</p>
                        </div>
                    </div>
                </div>
                
                <!-- Features FAQs -->
                <div class="faq-category" id="features-category" style="display: none;">
                    <h2 class="faq-category-title">Features Questions</h2>
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            <span>How does the waste tracking feature work?</span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Our waste tracking feature allows you to:</p>
                            <ul>
                                <li>Log different types of waste (recycling, compost, landfill)</li>
                                <li>Scan product barcodes to identify recyclability</li>
                                <li>Take photos of your waste for automatic categorization</li>
                                <li>View trends and patterns in your waste generation</li>
                                <li>Set reduction goals and track your progress</li>
                            </ul>
                            <p>The more you use it, the better insights you'll get about your waste habits!</p>
                        </div>
                    </div>
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            <span>What's included in the recycling rewards program?</span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Our recycling rewards program gives you points for:</p>
                            <ul>
                                <li>Properly recycling items (verified through photos or receipts)</li>
                                <li>Reducing your overall waste output</li>
                                <li>Participating in community clean-up events</li>
                                <li>Referring friends to Waste Wizard</li>
                            </ul>
                            <p>Points can be redeemed for:</p>
                            <ul>
                                <li>Discounts at eco-friendly businesses</li>
                                <li>Donations to environmental organizations</li>
                                <li>Exclusive Waste Wizard merchandise</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            <span>How accurate is the collection scheduling feature?</span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Our collection scheduling feature is highly accurate because:</p>
                            <ul>
                                <li>We integrate directly with many municipal waste services</li>
                                <li>Our algorithms account for holidays and weather delays</li>
                                <li>Users can report any discrepancies to improve accuracy</li>
                                <li>We provide real-time updates if schedules change</li>
                            </ul>
                            <p>For private waste services, you can customize your schedule and our system will learn your patterns over time.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            <span>Can Waste Wizard help me compost?</span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Absolutely! Our composting features include:</p>
                            <ul>
                                <li>A comprehensive guide to what can be composted</li>
                                <li>Local composting facility locator</li>
                                <li>Reminders to turn your compost pile</li>
                                <li>Tracking of your compost contributions</li>
                                <li>Tips for troubleshooting common composting issues</li>
                            </ul>
                            <p>We also partner with community composting programs in many areas.</p>
                        </div>
                    </div>
                </div>
                
                <!-- Billing FAQs -->
                <div class="faq-category" id="billing-category" style="display: none;">
                    <h2 class="faq-category-title">Billing Questions</h2>
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            <span>What payment methods do you accept?</span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>We accept all major credit cards (Visa, Mastercard, American Express, Discover) and PayPal for monthly subscriptions. For annual payments, we also accept bank transfers. Enterprise customers may be eligible for invoice billing.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            <span>Can I cancel my subscription anytime?</span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Yes, you can cancel your Pro subscription at any time. When you cancel:</p>
                            <ul>
                                <li>You'll continue to have access until the end of your current billing period</li>
                                <li>Your account will automatically revert to our free Basic plan</li>
                                <li>No further charges will be applied</li>
                            </ul>
                            <p>To cancel, go to your Account Settings and click "Cancel Subscription."</p>
                        </div>
                    </div>
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            <span>Do you offer refunds?</span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>We offer a 30-day money-back guarantee for all paid plans. If you're not satisfied with Waste Wizard Pro within the first 30 days of your subscription, we'll give you a full refund, no questions asked.</p>
                            <p>After 30 days, we don't typically offer prorated refunds for partial months, but we're happy to work with you if you're experiencing any issues with our service.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            <span>Do you offer discounts for non-profits or educational institutions?</span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Yes! We offer a 20% discount for registered non-profit organizations and educational institutions. To qualify:</p>
                            <ol>
                                <li>Sign up for a Pro account</li>
                                <li>Contact our support team with proof of your non-profit/educational status</li>
                                <li>We'll apply the discount to your subscription</li>
                            </ol>
                            <p>This discount applies to both monthly and annual plans.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact CTA -->
    <section class="contact-cta">
        <div class="container">
            <h2>Still have questions?</h2>
            <p>Our support team is happy to help with any other questions you might have about Waste Wizard.</p>
            <a href="contact.html" class="btn btn-solid">Contact Support</a>
        </div>
    </section>

    <!-- Footer -->
    <?php include("footer.php");?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // FAQ accordion functionality
            const faqQuestions = document.querySelectorAll('.faq-question');
            
            faqQuestions.forEach(question => {
                question.addEventListener('click', function() {
                    const faqItem = this.parentElement;
                    faqItem.classList.toggle('active');
                    
                    // Close other open FAQs in the same category
                    const category = faqItem.parentElement;
                    const itemsInCategory = category.querySelectorAll('.faq-item');
                    
                    itemsInCategory.forEach(item => {
                        if (item !== faqItem && item.classList.contains('active')) {
                            item.classList.remove('active');
                        }
                    });
                });
            });
            
            // Category tab functionality
            const categoryCards = document.querySelectorAll('.category-card');
            const faqCategories = document.querySelectorAll('.faq-category');
            
            categoryCards.forEach(card => {
                card.addEventListener('click', function() {
                    const category = this.getAttribute('data-category');
                    
                    // Update active category card
                    categoryCards.forEach(c => c.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Show selected FAQ category
                    faqCategories.forEach(cat => {
                        if (cat.id === `${category}-category`) {
                            cat.style.display = 'block';
                        } else {
                            cat.style.display = 'none';
                        }
                    });
                });
            });
            
            // Simple search functionality
            const searchInput = document.querySelector('.search-bar input');
            
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const faqItems = document.querySelectorAll('.faq-item');
                
                faqItems.forEach(item => {
                    const question = item.querySelector('.faq-question span').textContent.toLowerCase();
                    const answer = item.querySelector('.faq-answer').textContent.toLowerCase();
                    
                    if (question.includes(searchTerm) || answer.includes(searchTerm)) {
                        item.style.display = 'block';
                        
                        // Show parent category if hidden
                        const category = item.closest('.faq-category');
                        if (category.style.display === 'none') {
                            category.style.display = 'block';
                            
                            // Activate corresponding category tab
                            const categoryId = category.id.replace('-category', '');
                            categoryCards.forEach(card => {
                                if (card.getAttribute('data-category') === categoryId) {
                                    card.classList.add('active');
                                } else {
                                    card.classList.remove('active');
                                }
                            });
                        }
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        });
    </script>
</body>
</html>