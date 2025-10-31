<?php

include_once('database/db.php');

include_once('functions/functions.php');
                     
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pricing | Waste Wizard - Smart Waste Management</title>
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
        
        /* Pricing Tabs */
        .pricing-tabs {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 40px;
        }
        
        .tab-btn {
            padding: 12px 25px;
            background-color: white;
            border: 1px solid var(--gray);
            border-radius: 30px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .tab-btn.active, .tab-btn:hover {
            background-color: var(--primary);
            color: white;
            border-color: var(--primary);
        }
        
        /* Pricing Plans */
        .pricing-section {
            padding: 80px 0;
        }
        
        .pricing-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }
        
        .pricing-card {
            background-color: white;
            border-radius: 10px;
            padding: 40px 30px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s, box-shadow 0.3s;
            position: relative;
            border: 1px solid var(--gray);
        }
        
        .pricing-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }
        
        .pricing-card.popular {
            border: 2px solid var(--primary);
        }
        
        .popular-badge {
            position: absolute;
            top: -12px;
            right: 20px;
            background-color: var(--primary);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }
        
        .pricing-card h3 {
            font-size: 24px;
            margin-bottom: 15px;
            color: var(--dark);
        }
        
        .price {
            font-size: 48px;
            font-weight: 700;
            margin-bottom: 20px;
            color: var(--dark);
        }
        
        .price span {
            font-size: 16px;
            font-weight: 400;
            color: #777;
        }
        
        .pricing-features {
            margin-bottom: 30px;
        }
        
        .pricing-features ul {
            list-style: none;
        }
        
        .pricing-features ul li {
            margin-bottom: 12px;
            position: relative;
            padding-left: 30px;
        }
        
        .pricing-features ul li::before {
            content: '\f00c';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            position: absolute;
            left: 0;
            top: 2px;
            color: var(--primary);
        }
        
        .pricing-features ul li.disabled::before {
            content: '\f00d';
            color: #ccc;
        }
        
        .pricing-btn {
            display: block;
            text-align: center;
            width: 100%;
            padding: 12px;
            border-radius: 6px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background-color: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: var(--primary-dark);
        }
        
        .btn-outline {
            border: 2px solid var(--primary);
            color: var(--primary);
            background: transparent;
        }
        
        .btn-outline:hover {
            background-color: var(--primary);
            color: white;
        }
        
        /* Feature Comparison */
        .comparison-section {
            padding: 80px 0;
            background-color: var(--primary-light);
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
        
        .comparison-table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        }
        
        .comparison-table th, .comparison-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid var(--gray);
        }
        
        .comparison-table th {
            background-color: var(--primary-light);
            color: var(--dark);
            font-weight: 600;
        }
        
        .comparison-table tr:last-child td {
            border-bottom: none;
        }
        
        .comparison-table .feature-name {
            font-weight: 500;
        }
        
        .comparison-table .available {
            color: var(--primary);
            font-weight: 600;
        }
        
        .comparison-table .not-available {
            color: #999;
        }
        
        /* FAQ Section */
        .faq-section {
            padding: 80px 0;
            background-color: white;
        }
        
        .faq-container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .faq-item {
            margin-bottom: 20px;
            border: 1px solid var(--gray);
            border-radius: 8px;
            overflow: hidden;
        }
        
        .faq-question {
            padding: 20px;
            background-color: var(--light);
            font-weight: 600;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .faq-question:hover {
            background-color: #f5f5f5;
        }
        
        .faq-answer {
            padding: 0 20px;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s, padding 0.3s;
        }
        
        .faq-item.active .faq-answer {
            padding: 20px;
            max-height: 500px;
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
            .pricing-grid {
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
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
            
            .pricing-tabs {
                flex-wrap: wrap;
            }
            
            .cta-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .comparison-table {
                display: block;
                overflow-x: auto;
            }
        }
        
        @media (max-width: 576px) {
            .page-header {
                padding: 80px 0 40px;
            }
            
            .page-header h1 {
                font-size: 28px;
            }
            
            .price {
                font-size: 36px;
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
            <h1>Simple, Transparent Pricing</h1>
            <p>Choose the perfect plan for your home or business. Start for free or upgrade for advanced features.</p>
        </div>
    </section>
 
    <?php
    try {
        $stmt = $pdo->prepare("SELECT 
                                service_name,
                                price,
                                features,
                                is_popular,
                                service_id
                            FROM service_pricing 
                            WHERE status = 'public'
                            ORDER BY 
                                CASE service_name
                                    WHEN 'Basic' THEN 1
                                    WHEN 'Pro' THEN 2
                                    WHEN 'Enterprise' THEN 3
                                    ELSE 4
                                END");
        $stmt->execute();
        $pricing_plans = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    ?>
    <!-- Pricing Section -->
    <section class="pricing-section">
        <div class="container">
            <div class="pricing-tabs">
                <button class="tab-btn active">For Homes</button>
                <button class="tab-btn">For Businesses</button>
                <button class="tab-btn">For Communities</button>
            </div>
            
            <div class="pricing-grid">
                <?php foreach ($pricing_plans as $plan): 
                    $features = json_decode($plan['features'], true);
                    $is_popular = $plan['is_popular'] == 1;
                    
                    $btn_class = $is_popular ? 'btn-primary' : 'btn-outline';
                    
                    if ($plan['service_name'] === 'Basic') {
                        $btn_text = 'Get Started';
                    } elseif ($plan['service_name'] === 'Pro') {
                        $btn_text = 'Start Free Trial';
                    } elseif ($plan['service_name'] === 'Enterprise') {
                        $btn_text = 'Contact Sales';
                    } else {
                        $btn_text = 'Learn More';
                    }
                    
                    if ($plan['service_name'] === 'Enterprise') {
                        $btn_url = 'contact?plan=' . urlencode($plan['service_id']);
                    } else {
                        $btn_url = 'signup?plan=' . urlencode($plan['service_id']);
                    }
                ?>
                <div class="pricing-card <?= $is_popular ? 'popular' : '' ?>">
                    <?php if ($is_popular): ?>
                    <div class="popular-badge">Most Popular</div>
                    <?php endif; ?>
                    
                    <h3><?= htmlspecialchars($plan['service_name']) ?></h3>
                    
                    <div class="price">
                        <?php if ($plan['service_name'] === 'Enterprise'): ?>
                            Custom
                        <?php else: ?>
                            $<?= htmlspecialchars($plan['price']) ?>
                        <?php endif; ?>
                        <span>/ month</span>
                    </div>
                    
                    <div class="pricing-features">
                        <ul>
                            <?php foreach ($features as $feature): ?>
                            <li class="<?= $feature['status'] ? '' : 'disabled' ?>">
                                <?= htmlspecialchars($feature['feature']) ?>
                             
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    
                    <a href="<?= $btn_url ?>" class="pricing-btn <?= $btn_class ?>">
                        <?= $btn_text ?>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <!-- Feature Comparison -->
    <section class="comparison-section">
        <div class="container">
            <div class="section-title">
                <h2>Plan Comparison</h2>
                <p>See how our plans stack up against each other</p>
            </div>
            <?php
            try {
                $stmt = $pdo->prepare("SELECT 
                                        feature, 
                                        basic, 
                                        pro, 
                                        enterprise 
                                    FROM feature_comparison 
                                    WHERE status = 'public'
                                    ORDER BY created_at");
                $stmt->execute();
                $features = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
            ?>
            <table class="comparison-table">
            <thead>
                <tr>
                    <th>Feature</th>
                    <th>Basic</th>
                    <th>Pro</th>
                    <th>Enterprise</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($features as $feature): ?>
                <tr>
                    <td class="feature-name"><?= htmlspecialchars($feature['feature']) ?></td>
                    <td class="<?= $feature['basic'] ? 'available' : 'not-available' ?>">
                        <?= $feature['basic'] ? '✓' : '✗' ?>
                    </td>
                    <td class="<?= $feature['pro'] ? 'available' : 'not-available' ?>">
                        <?= $feature['pro'] ? '✓' : '✗' ?>
                    </td>
                    <td class="<?= $feature['enterprise'] ? 'available' : 'not-available' ?>">
                        <?= $feature['enterprise'] ? '✓' : '✗' ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            </table>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq-section">
        <div class="container">
            <div class="section-title">
                <h2>Frequently Asked Questions</h2>
                <p>Find answers to common questions about our pricing and plans</p>
            </div>
            
            <?php
            // Fetch active FAQs from database
            try {
                $stmt = $pdo->prepare("SELECT 
                                        question, 
                                        answer, 
                                        link, 
                                        media 
                                    FROM faqs 
                                    WHERE status = 'public'
                                    ORDER BY created_at DESC");
                $stmt->execute();
                $faqs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
            ?>

            <div class="faq-container">
                <?php foreach ($faqs as $faq): ?>
                <div class="faq-item">
                    <div class="faq-question">
                        <span><?= htmlspecialchars($faq['question']) ?></span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p><?= htmlspecialchars($faq['answer']) ?></p>
                        
                        <?php if (!empty($faq['link'])): ?>
                        <div class="faq-link">
                            <a href="<?= htmlspecialchars($faq['link']) ?>" target="_blank" rel="noopener noreferrer">
                                Learn more <i class="fas fa-external-link-alt"></i>
                            </a>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($faq['media'])): 
                            // Determine if media is an image (simple check - you may need more robust detection)
                            $is_image = preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $faq['media']);
                        ?>
                        <div class="faq-media">
                            <?php if ($is_image): ?>
                            <img src="<?= htmlspecialchars($faq['media']) ?>" alt="FAQ illustration">
                            <?php else: ?>
                            <div class="media-embed">
                                <!-- For videos or other embeddable media -->
                                <iframe src="<?= htmlspecialchars($faq['media']) ?>" frameborder="0" allowfullscreen></iframe>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <h2>Ready to Get Started?</h2>
            <p>Join thousands of homes and businesses already reducing their environmental impact with Waste Wizard.</p>
            <div class="cta-buttons">
                <a href="#" class="btn btn-light">Start Free Trial</a>
                <a href="contact" class="btn btn-dark-outline">Contact Sales</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include("footer.php");?>

    <script>
        // Simple tab functionality
        document.addEventListener('DOMContentLoaded', function() {
            const tabBtns = document.querySelectorAll('.tab-btn');
            
            tabBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    // Remove active class from all buttons
                    tabBtns.forEach(b => b.classList.remove('active'));
                    // Add active class to clicked button
                    this.classList.add('active');
                    
                    // Here you would typically show different pricing based on the selected tab
                    // This is just a UI demo without actual filtering logic
                });
            });
            
            // FAQ accordion functionality
            const faqQuestions = document.querySelectorAll('.faq-question');
            
            faqQuestions.forEach(question => {
                question.addEventListener('click', function() {
                    const faqItem = this.parentElement;
                    faqItem.classList.toggle('active');
                    
                    // Close other open FAQs
                    faqQuestions.forEach(q => {
                        if (q !== this && q.parentElement.classList.contains('active')) {
                            q.parentElement.classList.remove('active');
                        }
                    });
                });
            });
        });
    </script>
</body>
</html>