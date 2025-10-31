<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy | Waste Wizard - Smart Waste Management</title>
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
        
        /* Privacy Content */
        .privacy-section {
            padding: 80px 0;
        }
        
        .privacy-container {
            background-color: white;
            border-radius: 10px;
            padding: 50px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        }
        
        .privacy-header {
            margin-bottom: 40px;
            text-align: center;
        }
        
        .privacy-header h2 {
            font-size: 32px;
            color: var(--dark);
            margin-bottom: 15px;
        }
        
        .privacy-header p {
            color: #555;
            font-size: 16px;
        }
        
        .privacy-content h3 {
            font-size: 24px;
            margin: 40px 0 20px;
            color: var(--dark);
            padding-bottom: 10px;
            border-bottom: 2px solid var(--primary-light);
        }
        
        .privacy-content h4 {
            font-size: 20px;
            margin: 30px 0 15px;
            color: var(--dark);
        }
        
        .privacy-content p {
            margin-bottom: 20px;
            color: #555;
            line-height: 1.8;
        }
        
        .privacy-content ul {
            margin-bottom: 20px;
            margin-left: 20px;
        }
        
        .privacy-content ul li {
            margin-bottom: 10px;
            color: #555;
        }
        
        .privacy-content .update-date {
            font-style: italic;
            color: #777;
            margin-top: 50px;
            border-top: 1px solid var(--gray);
            padding-top: 20px;
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
            
            .privacy-header h2 {
                font-size: 28px;
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
            
            .privacy-container {
                padding: 30px;
            }
            
            .privacy-content h3 {
                font-size: 22px;
            }
            
            .privacy-content h4 {
                font-size: 18px;
            }
        }
        
        @media (max-width: 576px) {
            .page-header {
                padding: 80px 0 40px;
            }
            
            .page-header h1 {
                font-size: 28px;
            }
            
            .privacy-container {
                padding: 20px;
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
            <h1>Privacy Policy</h1>
            <p>Your privacy is important to us. Learn how we collect, use, and protect your information.</p>
        </div>
    </section>

    <!-- Privacy Content -->
    <section class="privacy-section">
        <div class="container">
            <div class="privacy-container">
                <div class="privacy-header">
                    <h2>Waste Wizard Privacy Policy</h2>
                    <p>Last Updated: January 1, 2023</p>
                </div>
                
                <div class="privacy-content">
                    <p>This Privacy Policy describes how Waste Wizard ("we," "us," or "our") collects, uses, and discloses your personal information when you use our waste management application and related services (collectively, the "Service").</p>
                    
                    <h3>1. Information We Collect</h3>
                    <p>We collect several types of information from and about users of our Service:</p>
                    
                    <h4>1.1 Personal Information</h4>
                    <ul>
                        <li><strong>Account Information:</strong> When you create an account, we collect your name, email address, phone number, and password.</li>
                        <li><strong>Profile Information:</strong> You may provide additional information like your address, waste collection schedule, and payment information for premium services.</li>
                        <li><strong>Waste Data:</strong> We collect information about your waste generation, recycling habits, and disposal patterns that you input or that we collect automatically through your use of the Service.</li>
                    </ul>
                    
                    <h4>1.2 Usage Data</h4>
                    <p>We automatically collect information about how you interact with our Service, including:</p>
                    <ul>
                        <li>Device information (type, operating system, browser)</li>
                        <li>IP address and approximate location</li>
                        <li>Pages visited and features used</li>
                        <li>Dates and times of access</li>
                    </ul>
                    
                    <h3>2. How We Use Your Information</h3>
                    <p>We use the information we collect for the following purposes:</p>
                    <ul>
                        <li>To provide and maintain our Service</li>
                        <li>To personalize your experience and provide customized waste management recommendations</li>
                        <li>To process transactions and send you related information</li>
                        <li>To notify you about changes to our Service</li>
                        <li>To allow you to participate in interactive features of our Service</li>
                        <li>To provide customer support</li>
                        <li>To gather analysis or valuable information to improve our Service</li>
                        <li>To monitor the usage of our Service</li>
                        <li>To detect, prevent, and address technical issues</li>
                    </ul>
                    
                    <h3>3. How We Share Your Information</h3>
                    <p>We may share your personal information in the following situations:</p>
                    
                    <h4>3.1 Service Providers</h4>
                    <p>We may employ third-party companies and individuals to facilitate our Service ("Service Providers"), provide the Service on our behalf, perform Service-related services, or assist us in analyzing how our Service is used.</p>
                    
                    <h4>3.2 Municipal Waste Services</h4>
                    <p>With your consent, we may share your waste data with local municipal waste services to help improve community waste management programs.</p>
                    
                    <h4>3.3 Business Transfers</h4>
                    <p>If we are involved in a merger, acquisition, or asset sale, your personal information may be transferred.</p>
                    
                    <h4>3.4 Legal Requirements</h4>
                    <p>We may disclose your information if required to do so by law or in response to valid requests by public authorities.</p>
                    
                    <h3>4. Data Security</h3>
                    <p>We implement appropriate technical and organizational measures to protect your personal information:</p>
                    <ul>
                        <li>All data is encrypted in transit using SSL/TLS</li>
                        <li>Sensitive information is encrypted at rest</li>
                        <li>Regular security audits and penetration testing</li>
                        <li>Access controls and authentication protocols</li>
                    </ul>
                    <p>However, no method of transmission over the Internet or method of electronic storage is 100% secure, and we cannot guarantee absolute security.</p>
                    
                    <h3>5. Your Data Protection Rights</h3>
                    <p>Depending on your location, you may have certain rights regarding your personal information:</p>
                    <ul>
                        <li><strong>Access:</strong> Request copies of your personal data</li>
                        <li><strong>Rectification:</strong> Request correction of inaccurate or incomplete data</li>
                        <li><strong>Erasure:</strong> Request deletion of your personal data</li>
                        <li><strong>Restriction:</strong> Request restriction of processing your personal data</li>
                        <li><strong>Objection:</strong> Object to our processing of your personal data</li>
                        <li><strong>Portability:</strong> Request transfer of your data to another organization</li>
                        <li><strong>Withdraw Consent:</strong> Withdraw consent at any time where we rely on consent</li>
                    </ul>
                    <p>To exercise any of these rights, please contact us at privacy@wastewizard.com.</p>
                    
                    <h3>6. Children's Privacy</h3>
                    <p>Our Service is not intended for use by children under 13. We do not knowingly collect personally identifiable information from children under 13. If you become aware that a child has provided us with personal information, please contact us.</p>
                    
                    <h3>7. Changes to This Privacy Policy</h3>
                    <p>We may update our Privacy Policy from time to time. We will notify you of any changes by posting the new Privacy Policy on this page and updating the "Last Updated" date.</p>
                    <p>You are advised to review this Privacy Policy periodically for any changes. Changes to this Privacy Policy are effective when they are posted on this page.</p>
                    
                    <h3>8. Contact Us</h3>
                    <p>If you have any questions about this Privacy Policy, please contact us:</p>
                    <ul>
                        <li>By email: privacy@wastewizard.com</li>
                        <li>By mail: Waste Wizard Privacy Office, 123 Green Street, Eco City, EC 12345</li>
                    </ul>
                    
                    <p class="update-date">This Privacy Policy was last updated on January 1, 2023.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include("footer.php");?>
</body>
</html>