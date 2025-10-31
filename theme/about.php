<?php

include_once('database/db.php');

include_once('functions/functions.php');
                     
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us | Waste Wizard - Smart Waste Management</title>
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
        
        /* About Section */
        .about-section {
            padding: 80px 0;
        }
        
        .about-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 60px;
            align-items: center;
        }
        
        .about-image {
            position: relative;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
        }
        
        .about-image img {
            width: 100%;
            display: block;
            transition: transform 0.5s;
        }
        
        .about-image:hover img {
            transform: scale(1.05);
        }
        
        .about-content h2 {
            font-size: 32px;
            margin-bottom: 20px;
            color: var(--dark);
        }
        
        .about-content p {
            margin-bottom: 20px;
            color: #555;
        }
        
        .mission-vision {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 50px;
        }
        
        .mission-card, .vision-card {
            background-color: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        }
        
        .mission-card h3, .vision-card h3 {
            font-size: 24px;
            margin-bottom: 15px;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .mission-card h3 i {
            color: var(--primary);
        }
        
        .vision-card h3 i {
            color: var(--secondary);
        }
        
        /* Team Section */
        .team-section {
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
        
        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }
        
        .team-member {
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s;
        }
        
        .team-member:hover {
            transform: translateY(-10px);
        }
        
        .member-image {
            height: 250px;
            overflow: hidden;
        }
        
        .member-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }
        
        .team-member:hover .member-image img {
            transform: scale(1.1);
        }
        
        .member-info {
            padding: 25px;
            text-align: center;
        }
        
        .member-info h4 {
            font-size: 20px;
            margin-bottom: 5px;
            color: var(--dark);
        }
        
        .member-info p {
            color: var(--primary);
            margin-bottom: 15px;
            font-weight: 500;
        }
        
        .member-social {
            display: flex;
            justify-content: center;
            gap: 15px;
        }
        
        .member-social a {
            width: 35px;
            height: 35px;
            background-color: var(--primary-light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            transition: all 0.3s;
        }
        
        .member-social a:hover {
            background-color: var(--primary);
            color: white;
        }
        
        /* Values Section */
        .values-section {
            padding: 80px 0;
            background-color: white;
        }
        
        .values-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }
        
        .value-card {
            text-align: center;
            padding: 30px;
        }
        
        .value-icon {
            width: 80px;
            height: 80px;
            background-color: rgba(76, 175, 80, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 30px;
            color: var(--primary);
        }
        
        .value-card h3 {
            font-size: 20px;
            margin-bottom: 15px;
            color: var(--dark);
        }
        
        .value-card p {
            color: #555;
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
            .about-container {
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
            
            .about-content h2 {
                font-size: 28px;
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
            
            .mission-vision {
                grid-template-columns: 1fr;
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
            <h1>About Waste Wizard</h1>
            <p>Learn about our mission to revolutionize waste management through technology and innovation</p>
        </div>
    </section>


    <?php
    try {
        $stmt = $pdo->prepare("SELECT 
                                id, 
                                our_story, 
                                our_mission, 
                                our_vision, 
                                core_values, 
                                image, 
                                date_updated, 
                                updated_by, 
                                created_at 
                            FROM about_page 
                            WHERE status = 'public'");
        
        $stmt->execute();
        $about_data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($about_data) {
            // Decode the JSON core_values
            $about_data['core_values'] = json_decode($about_data['core_values'], true);
            
        } else {
            echo "No public about page found.";
        }
        
    } catch (PDOException $e) {
        // Handle database errors
        echo "Error: " . $e->getMessage();
    }
    ?>
    <!-- About Section -->
    <section class="about-section">
        <div class="container">
            <div class="about-container">
                <div class="about-image">
                    <img src="WM images/<?php echo $about_data['image'];?>" alt="Waste Wizard team working">
                </div>
                <?php if (!empty($about_data['our_story'])): ?>
                <div class="about-content">
                    <h2>Our Story</h2>
                    <?php 
                    $paragraphs = preg_split('/\R/', $about_data['our_story']);                   
                    foreach ($paragraphs as $paragraph): 
                        if (!empty(trim($paragraph))):
                    ?>
                        <p><?= htmlspecialchars(trim($paragraph)) ?></p>
                    <?php 
                        endif;
                    endforeach; 
                    ?>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="mission-vision">
                <div class="mission-card">
                    <h3><i class="fas fa-bullseye"></i> Our Mission</h3>
                    <p><?= !empty($about_data['our_mission']) ? $about_data['our_mission'] : 'No Mission Data Available'; ?></p>
                </div>
                <div class="vision-card">
                    <h3><i class="fas fa-eye"></i> Our Vision</h3>
                    <p><?= !empty($about_data['our_vision']) ? $about_data['our_vision'] : 'No Vision Data Available'; ?></p>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="team-section">
        <div class="container">
            <div class="section-title">
                <h2>Meet Our Team</h2>
                <p>The passionate individuals behind Waste Wizard's success</p>
            </div>
            <div class="team-grid">
                <?php
                try {
                    $stmt = $pdo->prepare("SELECT 
                                            id, 
                                            fullname, 
                                            position, 
                                            image, 
                                            social_handles 
                                        FROM team_page 
                                        WHERE status = 'active'
                                        ORDER BY created_at ASC");
                    $stmt->execute();
                    $team_members = $stmt->fetchAll(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                    echo "Error: " . $e->getMessage();
                }
                ?>
                <?php foreach ($team_members as $member): 
                    $social_handles = json_decode($member['social_handles'], true);
                    $social_icons = [
                        'LinkedIn' => 'fa-linkedin-in',
                        'Twitter' => 'fa-twitter',
                        'Facebook' => 'fa-facebook-f',
                        'Instagram' => 'fa-instagram',
                        'Email' => 'fas fa-envelope'
                    ];
                ?>
            <div class="team-member">
                <div class="member-image">
                    <img src="<?=$replaced = str_replace("../", "", htmlspecialchars($member['image'])); ?>" 
                        alt="<?= htmlspecialchars($member['fullname']) ?>">
                </div>
                <div class="member-info">
                    <h4><?= htmlspecialchars($member['fullname']) ?></h4>
                    <p><?= htmlspecialchars($member['position']) ?></p>
                    
                    <?php if (!empty($social_handles)): ?>
                    <div class="member-social">
                        <?php foreach ($social_handles as $social): 
                            $icon = $social_icons[$social['social_name']] ?? 'fa-share-nodes';
                        ?>
                        <a href="<?= htmlspecialchars($social['user_profile_link']) ?>" 
                        target="_blank"
                        aria-label="<?= htmlspecialchars($social['social_name']) ?>">
                            <i class="fab <?= htmlspecialchars($icon) ?>"></i>
                        </a>
                        <?php endforeach; ?>
                        <?php if (!array_key_exists('Email', array_column($social_handles, 'social_name'))): ?>
                        <a href="mailto:<?= htmlspecialchars($member['email'] ?? '') ?>" 
                        aria-label="Email">
                            <i class="fas fa-envelope"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>

            </div>
        </div>
    </section>

    <!-- Values Section -->
    <section class="values-section">
        <div class="container">
            <div class="section-title">
                <h2>Our Core Values</h2>
                <p>The principles that guide everything we do</p>
            </div>
            <?php if (!empty($about_data['core_values'])): ?>
                <div class="values-grid">
                    <?php 
                    $value_icons = [
                        'Sustainability' => 'fa-leaf',
                        'Innovation' => 'fa-lightbulb',
                        'Community' => 'fa-users',
                        'Transparency' => 'fa-chart-line'
                    ];
                    
                    foreach ($about_data['core_values'] as $value): 
                        // Default icon if not specifically defined
                        $icon = $value_icons[$value['title']] ?? 'fa-star';
                    ?>
                    <div class="value-card">
                        <div class="value-icon">
                            <i class="fas <?= htmlspecialchars($icon) ?>"></i>
                        </div>
                        <h3><?= htmlspecialchars($value['title']) ?></h3>
                        <p><?= htmlspecialchars($value['description']) ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
        <?php endif; ?>
        </div>
    </section>


    <section class="cta-section">
        <div class="container">
            <h2>Ready to Join the Waste Revolution?</h2>
            <p>Become part of our growing community committed to smarter waste management and a sustainable future.</p>
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