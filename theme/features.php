<?php

include_once('database/db.php');

include_once('functions/functions.php');
                     
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Features | Waste Wizard - Smart Waste Management</title>
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
        
        /* Features Overview */
        .features-overview {
            padding: 80px 0;
        }
        
        .features-tabs {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 40px;
            flex-wrap: wrap;
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
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }
        
        .feature-card {
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }
        
        .feature-image {
            height: 200px;
            overflow: hidden;
        }
        
        .feature-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }
        
        .feature-card:hover .feature-image img {
            transform: scale(1.1);
        }
        
        .feature-content {
            padding: 25px;
        }
        
        .feature-content h3 {
            font-size: 22px;
            margin-bottom: 15px;
            color: var(--dark);
        }
        
        .feature-content p {
            color: #555;
            margin-bottom: 20px;
        }
        
        .feature-meta {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .feature-meta span {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 14px;
            color: #777;
        }
        
        .feature-meta i {
            color: var(--primary);
        }
        
        /* Feature Highlights */
        .feature-highlights {
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
        
        .highlight-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 50px;
            align-items: center;
        }
        
        .highlight-image {
            position: relative;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
        }
        
        .highlight-image img {
            width: 100%;
            display: block;
        }
        
        .highlight-content h3 {
            font-size: 28px;
            margin-bottom: 20px;
            color: var(--dark);
        }
        
        .highlight-content ul {
            list-style: none;
        }
        
        .highlight-content ul li {
            margin-bottom: 15px;
            position: relative;
            padding-left: 30px;
        }
        
        .highlight-content ul li::before {
            content: '\f00c';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            position: absolute;
            left: 0;
            top: 2px;
            color: var(--primary);
        }
        
        /* Feature Comparison */
        .feature-comparison {
            padding: 80px 0;
            background-color: white;
        }
        
        .comparison-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
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
            .highlight-container {
                gap: 40px;
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
            
            .highlight-content h3 {
                font-size: 24px;
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
            
            .features-tabs {
                flex-direction: column;
                align-items: center;
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
            <h1>Waste Wizard Features</h1>
            <p>Discover how our powerful tools can transform your waste management process and help you achieve your sustainability goals</p>
        </div>
    </section>
    <!-- Features Overview -->
    <?php
    try {
        $stmt = $pdo->prepare("SELECT 
                                id, 
                                title, 
                                content, 
                                category, 
                                image 
                            FROM features_page 
                            WHERE status = 'public'
                            ORDER BY created_at DESC");
        $stmt->execute();
        $features = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    $features_by_category = [
        'all' => $features,
        'Home' => array_filter($features, function($f) { return strpos($f['category'], 'Home') !== false; }),
        'Business' => array_filter($features, function($f) { return strpos($f['category'], 'Business') !== false; }),
        'Community' => array_filter($features, function($f) { return strpos($f['category'], 'Community') !== false; })
    ];
    ?>

    <section class="features-overview">
        <div class="container">
            <div class="features-tabs">
                <button class="tab-btn active" data-category="all">All Features</button>
                <button class="tab-btn" data-category="Home">For Homes</button>
                <button class="tab-btn" data-category="Business">For Businesses</button>
                <button class="tab-btn" data-category="Community">For Communities</button>
            </div>
            
            <div class="features-grid">
                <?php foreach ($features as $feature): 
                    $categories = explode(',', $feature['category']);
                ?>
                <div class="feature-card" data-categories="<?= htmlspecialchars($feature['category']) ?>">
                    <div class="feature-image">
                        <img src="WM images/<?= htmlspecialchars($feature['image']) ?>" alt="<?= htmlspecialchars($feature['title']) ?>">
                    </div>
                    <div class="feature-content">
                        <h3><?= htmlspecialchars($feature['title']) ?></h3>
                        <p><?= htmlspecialchars($feature['content']) ?></p>
                        <div class="feature-meta">
                            <?php foreach ($categories as $cat): 
                                $cat = trim($cat);
                                // Replace match() with switch for PHP < 8.0
                                switch($cat) {
                                    case 'Home':
                                        $icon = 'fa-home';
                                        break;
                                    case 'Business':
                                        $icon = 'fa-building';
                                        break;
                                    case 'Community':
                                        $icon = 'fa-users';
                                        break;
                                    default:
                                        $icon = 'fa-star';
                                }
                            ?>
                            <span><i class="fas <?= $icon ?>"></i> <?= $cat ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <?php
    try {
        $stmt = $pdo->prepare("SELECT 
                                id, 
                                title, 
                                sub_features, 
                                image 
                            FROM feature_highlights 
                            WHERE status = 'public'
                            ORDER BY created_at DESC
                            LIMIT 2");
        $stmt->execute();
        $feature_highlights = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    ?>

    <!-- Feature Highlights -->
    <section class="feature-highlights">
        <div class="container">
            <div class="section-title">
                <h2>Key Feature Highlights</h2>
                <p>Explore some of our most powerful tools in detail</p>
            </div>
            
            <?php foreach ($feature_highlights as $index => $highlight): 
                $sub_features = json_decode($highlight['sub_features'], true);
                $is_even = ($index % 2 == 0);
            ?>
            <div class="highlight-container" <?= $index > 0 ? 'style="margin-top: 60px;"' : '' ?>>
                <?php if ($is_even): ?>
                <div class="highlight-image">
                    <img src="WM images/<?= htmlspecialchars($highlight['image']) ?>" alt="<?= htmlspecialchars($highlight['title']) ?>">
                </div>
                <?php endif; ?>
                
                <div class="highlight-content">
                    <h3><?= htmlspecialchars($highlight['title']) ?></h3>
                    <ul>
                        <?php foreach ($sub_features as $sub_feature): ?>
                        <li><?= htmlspecialchars($sub_feature['feature']) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                
                <?php if (!$is_even): ?>
                <div class="highlight-image">
                    <img src="WM images/<?= htmlspecialchars($highlight['image']) ?>" alt="<?= htmlspecialchars($highlight['title']) ?>">
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </section>


    <!-- Feature Comparison -->
    <section class="feature-comparison">
        <div class="container">
            <div class="section-title">
                <h2>Feature Comparison</h2>
                <p>See which features are available in each Waste Wizard plan</p>
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

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <h2>Ready to Transform Your Waste Management?</h2>
            <p>Join thousands of homes and businesses already reducing their environmental impact with Waste Wizard.</p>
            <div class="cta-buttons">
                <a href="pricing" class="btn btn-light">View Plans</a>
                <a href="contact" class="btn btn-dark-outline">Contact Sales</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include("footer.php");?>
   <script>
document.addEventListener('DOMContentLoaded', function() {
    const tabBtns = document.querySelectorAll('.tab-btn');
    const featureCards = document.querySelectorAll('.feature-card');
    
    // Store features data for JavaScript filtering
    const featuresData = <?= json_encode($features_by_category) ?>;
    
    tabBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            // Update active tab
            tabBtns.forEach(function(b) { b.classList.remove('active'); });
            this.classList.add('active');
            
            const category = this.dataset.category;
            
            // Show/hide features based on category
            featureCards.forEach(function(card) {
                if (category === 'all') {
                    card.style.display = 'block';
                } else {
                    const categories = card.dataset.categories.split(',')
                        .map(function(c) { return c.trim(); });
                    card.style.display = categories.includes(category) ? 'block' : 'none';
                }
            });
        });
    });
    
    // Initialize with all features visible
    tabBtns[0].click();
});
</script>
</body>
</html>