<?php
include_once('database/db.php');
include_once('functions/functions.php');

session_start();

$loginError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            throw new Exception('Username and password are required');
        }
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if (!$user) {
            throw new Exception('Invalid username or password');
        }

        if ($user['account_status'] !== 'active') {
            $status = $user['account_status'];
            if ($status === 'banned') {
                throw new Exception('This account has been banned');
            } elseif ($status === 'suspended') {
                throw new Exception('This account is currently suspended');
            } elseif ($status === 'deleted') {
                throw new Exception('This account has been deleted');
            }
        }

        if (!password_verify($password, $user['password'])) {
            throw new Exception('Invalid username or password');
        }

        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['usertype'] = $user['usertype'];
        $_SESSION['loggedin'] = true;

        if ($user['usertype'] === 'waste_user') {
            header("Location: users/dashboard");
            exit();
        } elseif ($user['usertype'] === 'waste_driver') {

            header("Location: drivers/dashboard");
            exit();
        } else {
            header("Location: index");
            exit();
        }

    } catch (Exception $e) {
        $loginError = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Waste Wizard | Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #0a3d5a, #1d7f97);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            overflow-x: hidden;
        }
        
        .container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.25);
            width: 100%;
            max-width: 500px;
            padding: 40px;
            position: relative;
            overflow: hidden;
            z-index: 10;
        }
        
        .container::before {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 200px;
            height: 200px;
            background: linear-gradient(45deg, rgba(76, 175, 80, 0.1), rgba(139, 195, 74, 0.1));
            border-radius: 50%;
        }
        
        .container::after {
            content: '';
            position: absolute;
            bottom: -80px;
            left: -80px;
            width: 250px;
            height: 250px;
            background: linear-gradient(45deg, rgba(255, 193, 7, 0.1), rgba(255, 152, 0, 0.1));
            border-radius: 50%;
        }
        
        .back-btn {
            position: absolute;
            top: 20px;
            left: 20px;
            background: #e8f5e9;
            border: none;
            border-radius: 50%;
            width: 45px;
            height: 45px;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 20;
        }
        
        .back-btn:hover {
            background: #c8e6c9;
            transform: translateX(-3px);
        }
        
        .back-btn i {
            font-size: 18px;
            color: #2e7d32;
        }
        
        .header {
            text-align: center;
            margin-bottom: 35px;
            position: relative;
            z-index: 15;
        }
        
        .header i {
            font-size: 64px;
            background: linear-gradient(135deg, #0a5a3e, #2e7d32);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 15px;
        }
        
        .header h1 {
            color: #1b5e20;
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        
        .header p {
            color: #7f8c8d;
            font-size: 16px;
        }
        
        .input-group {
            margin-bottom: 22px;
            position: relative;
            z-index: 15;
        }
        
        .input-group i {
            position: absolute;
            top: 16px;
            left: 18px;
            font-size: 18px;
            color: #2e7d32;
        }
        
        .input-group input,.form-control {
            width: 100%;
            padding: 16px 20px 16px 52px;
            border: 2px solid #e1e5f1;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s ease;
            color: #2c3e50;
        }
        
        .input-group input {
            border-color: #43a047;
            box-shadow: 0 0 0 3px rgba(67, 160, 71, 0.2);
            outline: none;
        }
        .form-control:focus {
            border-color: #43a047;
            box-shadow: 0 0 0 3px rgba(67, 160, 71, 0.2);
            outline: none;
        }
        
        .input-group .toggle-password {
            position: absolute;
            top: 16px;
            right: 18px;
            color: #7f8c8d;
            cursor: pointer;
            font-size: 18px;
            transition: color 0.2s;
        }
        
        .input-group .toggle-password:hover {
            color: #2e7d32;
        }
        
        .terms {
            display: flex;
            align-items: flex-start;
            margin: 25px 0;
            position: relative;
            z-index: 15;
        }
        
        .terms input {
            margin-top: 4px;
            margin-right: 12px;
            accent-color: #2e7d32;
        }
        
        .terms label {
            color: #7f8c8d;
            font-size: 15px;
            line-height: 1.5;
        }
        
        .terms a {
            color: #2e7d32;
            text-decoration: none;
            font-weight: 500;
        }
        
        .terms a:hover {
            text-decoration: underline;
        }
        
        .signup-btn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #0a5a3e, #2e7d32);
            border: none;
            border-radius: 12px;
            color: white;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(10, 90, 62, 0.4);
            position: relative;
            overflow: hidden;
            z-index: 15;
        }
        
        .signup-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: 0.5s;
        }
        
        .signup-btn:hover::before {
            left: 100%;
        }
        
        .signup-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(10, 90, 62, 0.6);
        }
        
        .signup-btn:active {
            transform: translateY(1px);
        }
        
        .login-link {
            text-align: center;
            margin-top: 25px;
            color: #7f8c8d;
            z-index: 15;
            position: relative;
        }
        
        .login-link a {
            color: #2e7d32;
            text-decoration: none;
            font-weight: 600;
            margin-left: 5px;
        }
        
        .login-link a:hover {
            text-decoration: underline;
        }
        
        .error-message {
            color: #e74c3c;
            font-size: 14px;
            margin-top: 5px;
        }
        
        .eco-icons {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 25px;
            z-index: 15;
            position: relative;
        }
        
        .eco-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #e8f5e9;
            border: 2px solid #c8e6c9;
            transition: all 0.3s ease;
        }
        
        .eco-icon i {
            font-size: 24px;
            color: #2e7d32;
        }
        
        /* Notification styles */
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 25px;
            border-radius: 8px;
            color: white;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 1000;
            animation: slideIn 0.3s ease-out;
            max-width: 350px;
        }
        
        .notification.success {
            background: #2ecc71;
            border-left: 5px solid #27ae60;
        }
        
        .notification.error {
            background: #e74c3c;
            border-left: 5px solid #c0392b;
        }
        
        .notification i {
            font-size: 20px;
        }
        
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }
        
        /* Waste management decorative elements */
        .waste-icon {
            position: absolute;
            font-size: 24px;
            color: rgba(255, 255, 255, 0.3);
            z-index: 5;
        }
        
        .waste-1 { top: 10%; left: 5%; animation: float 8s infinite ease-in-out; }
        .waste-2 { top: 20%; right: 10%; animation: float 10s infinite ease-in-out; }
        .waste-3 { bottom: 15%; left: 15%; animation: float 12s infinite ease-in-out; }
        .waste-4 { bottom: 25%; right: 20%; animation: float 9s infinite ease-in-out; }
        
        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(10deg); }
        }
        
        /* Responsive styles */
        @media (max-width: 600px) {
            .container {
                padding: 30px 25px;
                border-radius: 16px;
            }
            
            .header h1 {
                font-size: 28px;
            }
            
            .header p {
                font-size: 15px;
            }
            
            .input-group input {
                padding: 14px 18px 14px 48px;
            }
            
            .notification {
                top: 10px;
                right: 10px;
                left: 10px;
                max-width: none;
            }
        }
        
        @media (max-width: 480px) {
            .header h1 {
                font-size: 24px;
            }
            
            .input-group input {
                font-size: 15px;
            }
            
            .signup-btn {
                padding: 14px;
                font-size: 16px;
            }
            
            .terms label {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <?php if ($loginError): ?>
    <div class="notification error">
        <i class="fas fa-exclamation-circle"></i>
        <span><?= htmlspecialchars($loginError) ?></span>
    </div>
    <script>
        setTimeout(() => {
            const notification = document.querySelector('.notification.error');
            if (notification) {
                notification.style.animation = 'slideOut 0.3s ease-out';
                setTimeout(() => notification.remove(), 300);
            }
        }, 5000);
    </script>
    <?php endif; ?>
    
    <div class="waste-icon waste-1"><i class="fas fa-recycle"></i></div>
    <div class="waste-icon waste-2"><i class="fas fa-trash-alt"></i></div>
    <div class="waste-icon waste-3"><i class="fas fa-leaf"></i></div>
    <div class="waste-icon waste-4"><i class="fas fa-trash-restore"></i></div>
    
    <div class="container">
        <button class="back-btn" onclick="window.history.back()">
            <i class="fas fa-arrow-left"></i>
        </button>
        
        <div class="header">
            <i class="fas fa-trash-restore"></i>
            <h1>Welcome Back to Waste Wizard</h1>
            <p>Log in to manage your waste services</p>
        </div>
        
        <form method="POST">
            <div class="input-group">
                <i class="fas fa-user"></i>
                <input type="text" id="username" name="username" placeholder="Username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
            </div>
            
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" id="password" name="password" placeholder="Password" required>
                <span class="toggle-password" onclick="togglePassword('password')">
                    <i class="fas fa-eye"></i>
                </span>
            </div>
            
            <div class="forgot-password" style="text-align: right; margin: 10px 0 20px;">
                <a href="forgot-password.php" style="color: #2e7d32; text-decoration: none;">Forgot password?</a>
            </div>
            
            <button type="submit" class="signup-btn">Log In</button>
        </form>
        
        <div class="eco-icons">
            <div class="eco-icon">
                <i class="fas fa-recycle"></i>
            </div>
            <div class="eco-icon">
                <i class="fas fa-trash-alt"></i>
            </div>
            <div class="eco-icon">
                <i class="fas fa-leaf"></i>
            </div>
            <div class="eco-icon">
                <i class="fas fa-trash-restore"></i>
            </div>
        </div>
        
        <div class="login-link">
            Don't have an account? <a href="usersignup.php">Sign up here</a>
        </div>
    </div>
    
    <script>
        // Simple password toggle functionality
        function togglePassword(fieldId) {
            const passwordInput = document.getElementById(fieldId);
            const eyeIcon = document.querySelector(`#${fieldId} + .toggle-password i`);
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>