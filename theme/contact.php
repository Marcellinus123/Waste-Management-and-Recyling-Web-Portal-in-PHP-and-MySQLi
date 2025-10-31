
<?php
include_once('database/db.php');

include_once('functions/functions.php');
     
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Get form data
        $fullname = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $subject = $_POST['subject'] ?? '';
        $message = $_POST['message'] ?? '';
        
        $source = $_SERVER['HTTP_REFERER'] ?? 'contact.php';
        
        $stmt = $pdo->prepare("INSERT INTO site_contacts 
                              (fullname, email, subject, message, source, date_contacted) 
                              VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$fullname, $email, $subject, $message, $source]);
        
        $submissionStatus = 'success';
        $submissionMessage = 'Your message has been sent successfully!';
        
        $_POST = [];
        
    } catch (Exception $e) {
        $submissionStatus = 'error';
        $submissionMessage = 'Error sending message: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us | Waste Wizard - Smart Waste Management</title>
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
        }
        
        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: -5px;
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
        }
        
        .btn-solid {
            background-color: var(--primary);
            color: white;
            border: 2px solid var(--primary);
        }
        
        .btn-solid:hover {
            background-color: var(--primary-dark);
        }
        
        /* Page Header */
        .page-header {
            padding: 80px 0 50px;
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
        
        /* Contact Section */
        .contact-section {
            padding: 80px 0;
        }
        
        .contact-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 50px;
        }
        
        .contact-info {
            background-color: white;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        }
        
        .contact-info h2 {
            font-size: 28px;
            margin-bottom: 25px;
            color: var(--dark);
        }
        
        .contact-method {
            display: flex;
            align-items: flex-start;
            gap: 20px;
            margin-bottom: 25px;
        }
        
        .contact-icon {
            width: 50px;
            height: 50px;
            background-color: var(--primary-light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 20px;
        }
        
        .contact-details h4 {
            font-size: 18px;
            margin-bottom: 5px;
            color: var(--dark);
        }
        
        .contact-details p, .contact-details a {
            color: #555;
            text-decoration: none;
        }
        
        .contact-details a:hover {
            color: var(--primary);
        }
        
        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }
        
        .social-links a {
            width: 40px;
            height: 40px;
            background-color: var(--primary-light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            transition: all 0.3s;
        }
        
        .social-links a:hover {
            background-color: var(--primary);
            color: white;
            transform: translateY(-3px);
        }
        
        /* Contact Form */
        .contact-form {
            background-color: white;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        }
        
        .contact-form h2 {
            font-size: 28px;
            margin-bottom: 25px;
            color: var(--dark);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark);
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--gray);
            border-radius: 6px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary);
        }
        
        textarea.form-control {
            min-height: 150px;
            resize: vertical;
        }
        
        .submit-btn {
            background-color: var(--primary);
            color: white;
            border: none;
            padding: 12px 30px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .submit-btn:hover {
            background-color: var(--primary-dark);
        }
        
        /* Map Section */
        .map-section {
            padding: 0 0 80px;
        }
        
        .map-container {
            height: 400px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }
        
        .map-container iframe {
            width: 100%;
            height: 100%;
            border: none;
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
                font-size: 36px;
            }
            
            .contact-container {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 576px) {
            .page-header h1 {
                font-size: 30px;
            }
            
            .contact-info, .contact-form {
                padding: 30px;
            }
        }
        .toast {
            position: fixed;
            top: 100px;
            right: 20px;
            padding: 15px 25px;
            border-radius: 5px;
            color: white;
            font-weight: 500;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transform: translateX(150%);
            opacity: 0;
            transition: all 0.3s ease-in-out;
            z-index: 1000;
            display: none;
        }

        .toast-success {
            background-color: #2ecc71;
        }

        .toast-error {
            background-color: #e74c3c;
        }
    </style>
</head>
<body>
    <!-- Header -->
<?php include("navbar.php");?>

    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <h1>Contact Waste Wizard</h1>
            <p>Have questions about our waste management solutions? Get in touch with our team and we'll be happy to assist you.</p>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="contact-section">
        <div class="container">
            <div class="contact-container">
                <div class="contact-info">
                    <h2>Get In Touch</h2>
                    
                    <div class="contact-method">
                        <div class="contact-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="contact-details">
                            <h4>Our Location</h4>
                            <p>123 Green Street<br>Navrongo District, EC 12345<br>Ghana</p>
                        </div>
                    </div>
                    
                    <div class="contact-method">
                        <div class="contact-icon">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <div class="contact-details">
                            <h4>Phone</h4>
                            <p><a href="tel:+233543374376">+ (233) 543-374376</a></p>
                            <p>Mon-Fri: 9am-6pm EST</p>
                        </div>
                    </div>
                    
                    <div class="contact-method">
                        <div class="contact-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="contact-details">
                            <h4>Email</h4>
                            <p><a href="mailto:info@wastewizard.com">info@wastewizard.com</a></p>
                            <p>Response within 24 hours</p>
                        </div>
                    </div>
                    
                    <h3 style="margin-top: 40px; margin-bottom: 15px;">Follow Us</h3>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                
                <div class="contact-form">
                    <h2>Send Us a Message</h2>
                        <form action="contact" method="POST">
                            <div class="form-group">
                                <label for="name">Your Name</label>
                                <input type="text" id="name" name="name" class="form-control" required 
                                    value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <input type="email" id="email" name="email" class="form-control" required 
                                    value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="subject">Subject</label>
                                <input type="text" id="subject" name="subject" class="form-control" required 
                                    value="<?= htmlspecialchars($_POST['subject'] ?? '') ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="message">Your Message</label>
                                <textarea id="message" name="message" class="form-control" required><?= 
                                    htmlspecialchars($_POST['message'] ?? '') 
                                ?></textarea>
                            </div>
                            
                            <button type="submit" class="submit-btn">Send Message</button>
                        </form>
                </div>


            </div>
        </div>
    </section>
    <!-- Toast Notification -->
    <?php if (isset($submissionStatus)): ?>
    <div id="toast-notification" class="toast toast-<?= $submissionStatus ?>">
        <?= htmlspecialchars($submissionMessage) ?>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const toast = document.getElementById('toast-notification');
        if (toast) {
            toast.style.display = 'block';
            setTimeout(() => {
                toast.style.opacity = '1';
                toast.style.transform = 'translateX(0)';
            }, 10);
            
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(150%)';
                setTimeout(() => toast.remove(), 300);
            }, 5000);
            toast.addEventListener('click', () => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(150%)';
                setTimeout(() => toast.remove(), 300);
            });
        }
    });
    </script>
    <?php endif; ?>
    <!-- Map Section -->
    <section class="map-section">
        <div class="container">
            <div class="map-container">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d31345.17746100652!2d-1.1032501495157263!3d10.876408039602923!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xe2b5b1dee27e183%3A0x4a7d9b560aae53a9!2sC.%20K.%20Tedam%20University%20of%20Technology%20and%20Applied%20Science%20(CKT-UTAS)!5e0!3m2!1sen!2sgh!4v1754017841885!5m2!1sen!2sgh" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>
    </section>

    <!-- Footer -->
     <?php include("footer.php");?>
</body>
</html>