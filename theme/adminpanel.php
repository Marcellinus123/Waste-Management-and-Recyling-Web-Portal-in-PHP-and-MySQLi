<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Waste Wizard | Waste Management Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary: #2e7d32;
            --primary-dark: #1b5e20;
            --secondary: #ff9800;
            --accent: #4caf50;
            --light: #f5f7fa;
            --dark: #263238;
            --gray: #78909c;
            --light-gray: #cfd8dc;
            --danger: #f44336;
            --success: #66bb6a;
            --warning: #ffca28;
            --sidebar-width: 260px;
            --sidebar-collapsed: 80px;
            --header-height: 70px;
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #f0f2f5;
            color: var(--dark);
            overflow-x: hidden;
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(180deg, var(--primary-dark), var(--primary));
            color: white;
            height: 100vh;
            position: fixed;
            transition: var(--transition);
            z-index: 100;
            box-shadow: 2px 0 15px rgba(0, 0, 0, 0.15);
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }

        .sidebar.collapsed {
            width: var(--sidebar-collapsed);
        }

        .sidebar-header {
            display: flex;
            align-items: center;
            padding: 20px 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.15);
            height: var(--header-height);
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-icon {
            font-size: 28px;
            color: var(--secondary);
        }

        .logo-text {
            font-size: 22px;
            font-weight: 700;
            white-space: nowrap;
        }

        .sidebar.collapsed .logo-text {
            display: none;
        }

        .sidebar-menu {
            padding: 15px 0;
            flex: 1;
        }

        .menu-item {
            display: flex;
            align-items: center;
            padding: 14px 20px;
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            transition: var(--transition);
            font-size: 15px;
            border-left: 3px solid transparent;
            white-space: nowrap;
        }

        .menu-item:hover, .menu-item.active {
            background: rgba(255, 255, 255, 0.12);
            color: white;
            border-left: 3px solid var(--secondary);
        }

        .menu-item i {
            width: 30px;
            font-size: 18px;
            transition: var(--transition);
        }

        .menu-text {
            margin-left: 10px;
            transition: var(--transition);
        }

        .sidebar.collapsed .menu-text {
            opacity: 0;
            width: 0;
            overflow: hidden;
        }

        .sidebar-footer {
            padding: 15px;
            background: rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .admin-profile {
            display: flex;
            align-items: center;
            gap: 12px;
            width: 100%;
        }

        .admin-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: var(--light-gray);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border: 2px solid rgba(255, 255, 255, 0.3);
            flex-shrink: 0;
        }

        .admin-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .admin-info {
            display: flex;
            flex-direction: column;
            overflow: hidden;
            transition: var(--transition);
        }

        .admin-name {
            font-weight: 600;
            font-size: 14px;
            white-space: nowrap;
        }

        .admin-role {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.75);
            white-space: nowrap;
        }

        .sidebar.collapsed .admin-info {
            opacity: 0;
            width: 0;
        }

        .logout-btn {
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            font-size: 18px;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
            flex-shrink: 0;
            margin-left: auto;
        }

        .logout-btn:hover {
            background: rgba(255, 255, 255, 0.15);
        }

        /* Main Content Styles */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            transition: var(--transition);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .sidebar.collapsed ~ .main-content {
            margin-left: var(--sidebar-collapsed);
        }

        .topbar {
            height: var(--header-height);
            background: white;
            display: flex;
            align-items: center;
            padding: 0 25px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
            position: sticky;
            top: 0;
            z-index: 99;
        }

        .toggle-btn {
            background: none;
            border: none;
            font-size: 20px;
            color: var(--dark);
            cursor: pointer;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
            margin-right: 10px;
        }

        .toggle-btn:hover {
            background: var(--light);
        }

        .search-bar {
            position: relative;
            flex: 1;
            max-width: 500px;
        }

        .search-bar input {
            width: 100%;
            padding: 12px 15px 12px 45px;
            border: 1px solid var(--light-gray);
            border-radius: 30px;
            font-size: 15px;
            transition: var(--transition);
            background: #f8f9fa;
        }

        .search-bar input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.15);
            background: white;
        }

        .search-bar i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray);
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-left: auto;
        }

        .action-btn {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8f9fa;
            color: var(--dark);
            cursor: pointer;
            transition: var(--transition);
            position: relative;
            border: none;
        }

        .action-btn:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-2px);
        }

        .notification-badge {
            position: absolute;
            top: 5px;
            right: 5px;
            background: var(--danger);
            color: white;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            font-size: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        .user-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            overflow: hidden;
            border: 2px solid var(--primary);
            cursor: pointer;
        }

        .user-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .content-area {
            padding: 30px;
            flex: 1;
        }

        .page-title {
            font-size: 28px;
            margin-bottom: 25px;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 600;
        }

        .page-title i {
            background: var(--primary);
            color: white;
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 35px;
        }

        .card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            padding: 25px;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
            background: var(--primary);
        }

        .stat-card {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .stat-icon {
            width: 70px;
            height: 70px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            color: white;
            flex-shrink: 0;
        }

        .bg-primary {
            background: var(--primary);
        }

        .bg-success {
            background: var(--success);
        }

        .bg-warning {
            background: var(--warning);
        }

        .bg-accent {
            background: var(--accent);
        }

        .stat-info h3 {
            font-size: 28px;
            margin-bottom: 5px;
            font-weight: 600;
        }

        .stat-info p {
            color: var(--gray);
            font-size: 15px;
        }

        .dashboard-row {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 25px;
            margin-bottom: 35px;
        }

        .section-title {
            font-size: 19px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 600;
            color: var(--dark);
            padding-bottom: 12px;
            border-bottom: 1px solid var(--light);
        }

        .section-title i {
            color: var(--primary);
            font-size: 20px;
        }

        .recent-collections {
            min-height: 300px;
        }

        .collection-list {
            list-style: none;
        }

        .collection-item {
            display: flex;
            align-items: center;
            padding: 16px 0;
            border-bottom: 1px solid var(--light);
            transition: var(--transition);
        }

        .collection-item:hover {
            background: rgba(46, 125, 50, 0.03);
            border-radius: 8px;
            padding: 16px 10px;
        }

        .collection-item:last-child {
            border-bottom: none;
        }

        .collection-icon {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            background: rgba(46, 125, 50, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 18px;
            color: var(--primary);
            font-size: 18px;
            flex-shrink: 0;
        }

        .collection-info {
            flex: 1;
        }

        .collection-info h4 {
            margin-bottom: 5px;
            font-weight: 600;
            font-size: 16px;
        }

        .collection-info p {
            font-size: 14px;
            color: var(--gray);
        }

        .collection-status {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 500;
            min-width: 100px;
            text-align: center;
        }

        .status-completed {
            background: rgba(102, 187, 106, 0.15);
            color: var(--success);
        }

        .status-pending {
            background: rgba(255, 152, 0, 0.15);
            color: var(--warning);
        }

        .status-delayed {
            background: rgba(244, 67, 54, 0.15);
            color: var(--danger);
        }

        .messages-container {
            min-height: 300px;
        }

        .message-list {
            list-style: none;
            max-height: 400px;
            overflow-y: auto;
            padding-right: 10px;
        }

        .message-list::-webkit-scrollbar {
            width: 6px;
        }

        .message-list::-webkit-scrollbar-thumb {
            background: rgba(46, 125, 50, 0.3);
            border-radius: 10px;
        }

        .message-item {
            padding: 18px;
            border-radius: 12px;
            background: #f8f9fa;
            margin-bottom: 15px;
            cursor: pointer;
            transition: var(--transition);
            border: 1px solid #e9ecef;
        }

        .message-item:hover {
            background: #e3f2fd;
            transform: translateY(-2px);
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
        }

        .message-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .message-sender {
            font-weight: 600;
            color: var(--primary-dark);
            font-size: 15px;
        }

        .message-time {
            font-size: 13px;
            color: var(--gray);
        }

        .message-preview {
            font-size: 14px;
            color: var(--dark);
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            line-height: 1.5;
        }

        .unread {
            background: white;
            border-left: 4px solid var(--primary);
        }

        /* Profile Upload */
        .profile-upload {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
        }

        .avatar-upload {
            position: relative;
            width: 160px;
            height: 160px;
        }

        .avatar-preview {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--primary);
            background: var(--light);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .upload-btn {
            position: absolute;
            bottom: 12px;
            right: 12px;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border: none;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.2);
            transition: var(--transition);
        }

        .upload-btn:hover {
            transform: scale(1.1);
            background: var(--primary-dark);
        }

        .upload-btn i {
            font-size: 20px;
        }

        .profile-form {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 10px;
            font-weight: 500;
            color: var(--dark);
            font-size: 15px;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 14px 18px;
            border: 1px solid var(--light-gray);
            border-radius: 10px;
            font-size: 16px;
            transition: var(--transition);
            background: #f8f9fa;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(46, 125, 50, 0.15);
            background: white;
        }

        .btn {
            padding: 14px 30px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
            box-shadow: 0 4px 15px rgba(46, 125, 50, 0.3);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(46, 125, 50, 0.4);
        }

        /* Chart container */
        .chart-container {
            height: 300px;
            margin-top: 25px;
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .dashboard-row {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.collapsed {
                transform: translateX(0);
                width: var(--sidebar-collapsed);
            }
            
            .sidebar.active {
                transform: translateX(0);
                width: var(--sidebar-width);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .search-bar {
                max-width: 300px;
            }
        }

        @media (max-width: 768px) {
            .topbar {
                padding: 0 15px;
            }
            
            .content-area {
                padding: 20px 15px;
            }
            
            .dashboard-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .page-title {
                font-size: 24px;
            }
            
            .search-bar {
                display: none;
            }
            
            .topbar-actions {
                gap: 10px;
            }
        }

        /* Page content */
        .page-content {
            display: none;
            animation: fadeIn 0.4s ease;
        }
        
        .page-content.active {
            display: block;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Feedback card */
        .feedback-card {
            display: flex;
            gap: 20px;
            padding: 20px;
            border-radius: 12px;
            background: white;
            margin-bottom: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            border-left: 4px solid var(--secondary);
        }
        
        .feedback-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            overflow: hidden;
            flex-shrink: 0;
            background: var(--light);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 20px;
        }
        
        .feedback-content {
            flex: 1;
        }
        
        .feedback-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        
        .feedback-user {
            font-weight: 600;
            color: var(--dark);
        }
        
        .feedback-date {
            color: var(--gray);
            font-size: 13px;
        }
        
        .feedback-rating {
            color: var(--warning);
            margin-bottom: 8px;
        }
        
        .feedback-text {
            color: var(--dark);
            line-height: 1.6;
        }
        
        /* Payment cards */
        .payment-card {
            display: flex;
            align-items: center;
            padding: 15px;
            border-radius: 10px;
            background: white;
            margin-bottom: 15px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.05);
            border-left: 4px solid var(--success);
        }
        
        .payment-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            background: rgba(102, 187, 106, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--success);
            font-size: 20px;
            margin-right: 15px;
            flex-shrink: 0;
        }
        
        .payment-info {
            flex: 1;
        }
        
        .payment-info h4 {
            margin-bottom: 5px;
            font-weight: 600;
        }
        
        .payment-info p {
            color: var(--gray);
            font-size: 14px;
        }
        
        .payment-actions {
            display: flex;
            gap: 10px;
        }
        
        .btn-sm {
            padding: 8px 15px;
            font-size: 14px;
        }
        
        /* Mobile menu button */
        .mobile-menu-btn {
            display: none;
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: var(--primary);
            color: white;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            z-index: 101;
            border: none;
            font-size: 24px;
            justify-content: center;
            align-items: center;
        }
        
        @media (max-width: 992px) {
            .mobile-menu-btn {
                display: flex;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="logo">
                <i class="fas fa-recycle logo-icon"></i>
                <span class="logo-text">Waste Wizard</span>
            </div>
        </div>
        
        <div class="sidebar-menu">
            <a href="#dashboard" class="menu-item active" data-page="dashboard">
                <i class="fas fa-home"></i>
                <span class="menu-text">Dashboard</span>
            </a>
            <a href="#users" class="menu-item" data-page="users">
                <i class="fas fa-users"></i>
                <span class="menu-text">User Management</span>
            </a>
            <a href="#messages" class="menu-item" data-page="messages">
                <i class="fas fa-comments"></i>
                <span class="menu-text">Messages</span>
            </a>
            <a href="#profile" class="menu-item" data-page="profile">
                <i class="fas fa-user"></i>
                <span class="menu-text">Admin Profile</span>
            </a>
            <a href="#payments" class="menu-item" data-page="payments">
                <i class="fas fa-credit-card"></i>
                <span class="menu-text">Payments</span>
            </a>
            <a href="#collection" class="menu-item" data-page="collection">
                <i class="fas fa-trash-alt"></i>
                <span class="menu-text">Waste Collection</span>
            </a>
            <a href="#analytics" class="menu-item" data-page="analytics">
                <i class="fas fa-chart-line"></i>
                <span class="menu-text">Analytics</span>
            </a>
            <a href="#vehicles" class="menu-item" data-page="vehicles">
                <i class="fas fa-truck"></i>
                <span class="menu-text">Vehicles</span>
            </a>
            <a href="#driver-feedback" class="menu-item" data-page="driver-feedback">
                <i class="fas fa-star-half-alt"></i>
                <span class="menu-text">Driver Feedback & Report</span>
            </a>
            <a href="#zones" class="menu-item" data-page="zones">
                <i class="fas fa-map-marked-alt"></i>
                <span class="menu-text">Zones</span>
            </a>
            <a href="#user-feedback" class="menu-item" data-page="user-feedback">
                <i class="fas fa-comment-dots"></i>
                <span class="menu-text">User Feedback</span>
            </a>
            <a href="#settings" class="menu-item" data-page="settings">
                <i class="fas fa-cog"></i>
                <span class="menu-text">Settings</span>
            </a>
        </div>
        
        <div class="sidebar-footer">
            <div class="admin-profile">
                <div class="admin-avatar">
                    <img src="" alt="Admin" id="adminAvatar">
                </div>
                <div class="admin-info">
                    <span class="admin-name">Admin User</span>
                    <span class="admin-role">Super Admin</span>
                </div>
            </div>
            <button class="logout-btn" id="logoutBtn">
                <i class="fas fa-sign-out-alt"></i>
            </button>
        </div>
    </aside>
    
    <!-- Main Content -->
    <main class="main-content">
        <div class="topbar">
            <button class="toggle-btn" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <div class="search-bar">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search dashboard...">
            </div>
            <div class="topbar-actions">
                <button class="action-btn">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge">5</span>
                </button>
                <button class="action-btn">
                    <i class="fas fa-cog"></i>
                </button>
                <div class="user-avatar">
                    <img src="" alt="User" id="topbarAvatar">
                </div>
            </div>
        </div>
        
        <div class="content-area">
            <!-- Dashboard Page -->
            <div class="page-content active" id="dashboardPage">
                <h1 class="page-title"><i class="fas fa-home"></i> Dashboard Overview</h1>
                
                <div class="dashboard-grid">
                    <div class="card stat-card">
                        <div class="stat-icon bg-primary">
                            <i class="fas fa-trash"></i>
                        </div>
                        <div class="stat-info">
                            <h3>1,850 kg</h3>
                            <p>Waste Collected Today</p>
                        </div>
                    </div>
                    
                    <div class="card stat-card">
                        <div class="stat-icon bg-success">
                            <i class="fas fa-recycle"></i>
                        </div>
                        <div class="stat-info">
                            <h3>78%</h3>
                            <p>Recycling Rate</p>
                        </div>
                    </div>
                    
                    <div class="card stat-card">
                        <div class="stat-icon bg-warning">
                            <i class="fas fa-truck"></i>
                        </div>
                        <div class="stat-info">
                            <h3>12/15</h3>
                            <p>Active Vehicles</p>
                        </div>
                    </div>
                    
                    <div class="card stat-card">
                        <div class="stat-icon bg-accent">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-info">
                            <h3>4,852</h3>
                            <p>Active Users</p>
                        </div>
                    </div>
                </div>
                
                <div class="dashboard-row">
                    <div class="card recent-collections">
                        <h2 class="section-title"><i class="fas fa-list"></i> Recent Collections</h2>
                        <ul class="collection-list">
                            <li class="collection-item">
                                <div class="collection-icon">
                                    <i class="fas fa-trash"></i>
                                </div>
                                <div class="collection-info">
                                    <h4>Zone A - Residential</h4>
                                    <p>Completed 9:30 AM | 250 kg collected</p>
                                </div>
                                <span class="collection-status status-completed">Completed</span>
                            </li>
                            <li class="collection-item">
                                <div class="collection-icon">
                                    <i class="fas fa-recycle"></i>
                                </div>
                                <div class="collection-info">
                                    <h4>Zone B - Commercial</h4>
                                    <p>In progress | Estimated 320 kg</p>
                                </div>
                                <span class="collection-status status-pending">In Progress</span>
                            </li>
                            <li class="collection-item">
                                <div class="collection-icon">
                                    <i class="fas fa-trash"></i>
                                </div>
                                <div class="collection-info">
                                    <h4>Zone D - Industrial</h4>
                                    <p>Scheduled 2:00 PM | Estimated 450 kg</p>
                                </div>
                                <span class="collection-status status-pending">Scheduled</span>
                            </li>
                            <li class="collection-item">
                                <div class="collection-icon">
                                    <i class="fas fa-recycle"></i>
                                </div>
                                <div class="collection-info">
                                    <h4>Zone C - Residential</h4>
                                    <p>Completed 11:15 AM | 180 kg collected</p>
                                </div>
                                <span class="collection-status status-completed">Completed</span>
                            </li>
                            <li class="collection-item">
                                <div class="collection-icon">
                                    <i class="fas fa-trash"></i>
                                </div>
                                <div class="collection-info">
                                    <h4>Zone E - Market Area</h4>
                                    <p>Delayed | Technical issue</p>
                                </div>
                                <span class="collection-status status-delayed">Delayed</span>
                            </li>
                        </ul>
                    </div>
                    
                    <div class="card messages-container">
                        <h2 class="section-title"><i class="fas fa-comments"></i> Recent Messages</h2>
                        <ul class="message-list">
                            <li class="message-item unread">
                                <div class="message-header">
                                    <span class="message-sender">John Bawuah</span>
                                    <span class="message-time">10:25 AM</span>
                                </div>
                                <div class="message-preview">
                                    My recycling bin hasn't been collected for two weeks. Can you please look into this?
                                </div>
                            </li>
                            <li class="message-item unread">
                                <div class="message-header">
                                    <span class="message-sender">Bolga Technical Uni.</span>
                                    <span class="message-time">9:45 AM</span>
                                </div>
                                <div class="message-preview">
                                    We would like to schedule a special e-waste collection for our building. When can...
                                </div>
                            </li>
                            <li class="message-item">
                                <div class="message-header">
                                    <span class="message-sender">Pongobu Guesthouse</span>
                                    <span class="message-time">Yesterday</span>
                                </div>
                                <div class="message-preview">
                                    Thank you for resolving my issue so quickly! The new bin works perfectly.
                                </div>
                            </li>
                            <li class="message-item">
                                <div class="message-header">
                                    <span class="message-sender">CKT-UTAS</span>
                                    <span class="message-time">Yesterday</span>
                                </div>
                                <div class="message-preview">
                                    Reminder: Monthly waste management meeting tomorrow at 10 AM in conference room B.
                                </div>
                            </li>
                            <li class="message-item">
                                <div class="message-header">
                                    <span class="message-sender">Dollar Hostel</span>
                                    <span class="message-time">Oct 12</span>
                                </div>
                                <div class="message-preview">
                                    I noticed one of your trucks leaking fluid on Oak Street. You might want to check that.
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <div class="dashboard-row">
                    <div class="card">
                        <h2 class="section-title"><i class="fas fa-exclamation-triangle"></i> Pending Issues</h2>
                        <ul class="collection-list">
                            <li class="collection-item">
                                <div class="collection-icon" style="background: rgba(244, 67, 54, 0.1); color: #f44336;">
                                    <i class="fas fa-truck"></i>
                                </div>
                                <div class="collection-info">
                                    <h4>Vehicle #TRK-045 Maintenance</h4>
                                    <p>Overdue for service | 15 days overdue</p>
                                </div>
                            </li>
                            <li class="collection-item">
                                <div class="collection-icon" style="background: rgba(255, 152, 0, 0.1); color: #ff9800;">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div class="collection-info">
                                    <h4>Zone G Route Optimization</h4>
                                    <p>Route needs optimization to reduce fuel consumption</p>
                                </div>
                            </li>
                            <li class="collection-item">
                                <div class="collection-icon" style="background: rgba(76, 175, 80, 0.1); color: #4caf50;">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div class="collection-info">
                                    <h4>New Staff Training</h4>
                                    <p>5 new drivers require waste management training</p>
                                </div>
                            </li>
                            <li class="collection-item">
                                <div class="collection-icon" style="background: rgba(121, 134, 203, 0.1); color: #7986cb;">
                                    <i class="fas fa-database"></i>
                                </div>
                                <div class="collection-info">
                                    <h4>Monthly Report Generation</h4>
                                    <p>Environmental impact report due in 3 days</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Admin Profile Page -->
            <div class="page-content" id="profilePage">
                <h1 class="page-title"><i class="fas fa-user"></i> Admin Profile</h1>
                
                <div class="card">
                    <div class="profile-upload">
                        <div class="avatar-upload">
                            <img src="" class="avatar-preview" id="avatarPreview" alt="Admin Avatar">
                            <button class="upload-btn" id="uploadBtn">
                                <i class="fas fa-camera"></i>
                            </button>
                            <input type="file" id="avatarInput" accept="image/*" style="display: none;">
                        </div>
                        
                        <div class="profile-form">
                            <div class="form-group">
                                <label for="adminName">Full Name</label>
                                <input type="text" id="adminName" value="Bernard Adjei">
                            </div>
                            
                            <div class="form-group">
                                <label for="adminEmail">Email Address</label>
                                <input type="email" id="adminEmail" value="badjei@example.com">
                            </div>
                            
                            <div class="form-group">
                                <label for="adminRole">Role</label>
                                <input type="text" id="adminRole" value="Super Admin" disabled>
                            </div>
                            
                            <div class="form-group">
                                <label for="adminPhone">Phone Number</label>
                                <input type="tel" id="adminPhone" value="+(233) 543-374376">
                            </div>
                            
                            <div class="form-group">
                                <label for="adminBio">Bio</label>
                                <textarea id="adminBio" rows="4">Senior administrator with 8 years of experience in waste management systems. Focused on sustainability and efficiency improvements. Certified in environmental management and circular economy principles.</textarea>
                            </div>
                            
                            <button class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Profile
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Payment Confirmation Page -->
            <div class="page-content" id="paymentsPage">
                <h1 class="page-title"><i class="fas fa-credit-card"></i> Payment Confirmations</h1>
                
                <div class="card">
                    <h2 class="section-title"><i class="fas fa-check-circle"></i> Pending Approvals</h2>
                    
                    <div class="payment-card">
                        <div class="payment-icon">
                            <i class="fas fa-home"></i>
                        </div>
                        <div class="payment-info">
                            <h4>Residential Service - Emmanuel Ansah</h4>
                            <p>Premium Plan • $29.99/month • July 15, 2025</p>
                        </div>
                        <div class="payment-actions">
                            <button class="btn btn-primary btn-sm">
                                <i class="fas fa-check"></i> Approve
                            </button>
                            <button class="btn" style="background: #f8f9fa; color: var(--danger);">
                                <i class="fas fa-times"></i> Reject
                            </button>
                        </div>
                    </div>
                    
                    <div class="payment-card">
                        <div class="payment-icon">
                            <i class="fas fa-building"></i>
                        </div>
                        <div class="payment-info">
                            <h4>Commercial Service - CKT-UTAS</h4>
                            <p>Enterprise Plan • $249.99/month • Jul 12, 2025</p>
                        </div>
                        <div class="payment-actions">
                            <button class="btn btn-primary btn-sm">
                                <i class="fas fa-check"></i> Approve
                            </button>
                            <button class="btn" style="background: #f8f9fa; color: var(--danger);">
                                <i class="fas fa-times"></i> Reject
                            </button>
                        </div>
                    </div>
                    
                    <div class="payment-card">
                        <div class="payment-icon">
                            <i class="fas fa-industry"></i>
                        </div>
                        <div class="payment-info">
                            <h4>Industrial Service - Tech Manufacturing Inc.</h4>
                            <p>Custom Plan • $1,499.99/month • Jul 12, 2025</p>
                        </div>
                        <div class="payment-actions">
                            <button class="btn btn-primary btn-sm">
                                <i class="fas fa-check"></i> Approve
                            </button>
                            <button class="btn" style="background: #f8f9fa; color: var(--danger);">
                                <i class="fas fa-times"></i> Reject
                            </button>
                        </div>
                    </div>
                    
                    <h2 class="section-title" style="margin-top: 30px;"><i class="fas fa-history"></i> Recent Payments</h2>
                    
                    <div class="payment-card" style="border-left-color: var(--gray);">
                        <div class="payment-icon" style="background: rgba(120, 144, 156, 0.15); color: var(--gray);">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="payment-info">
                            <h4>Residential Service - Alhassan Ayariga</h4>
                            <p>Basic Plan • $19.99/month • Approved Jul 10, 2025</p>
                        </div>
                    </div>
                    
                    <div class="payment-card" style="border-left-color: var(--gray);">
                        <div class="payment-icon" style="background: rgba(120, 144, 156, 0.15); color: var(--gray);">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="payment-info">
                            <h4>Commercial Service - Downtown Cafe</h4>
                            <p>Standard Plan • $99.99/month • Approved Jul 8, 2025</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Driver Feedback Page -->
            <div class="page-content" id="driver-feedbackPage">
                <h1 class="page-title"><i class="fas fa-star-half-alt"></i> Driver & Vehicle Feedbacks</h1>
                
                <div class="card">
                    <h2 class="section-title"><i class="fas fa-truck"></i> Vehicle Feedback</h2>
                    
                    <div class="feedback-card">
                        <div class="feedback-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="feedback-content">
                            <div class="feedback-header">
                                <span class="feedback-user">Robert Abanga</span>
                                <span class="feedback-date">Jul 03, 2025</span>
                            </div>
                            <div class="feedback-rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="far fa-star"></i>
                                <i class="far fa-star"></i>
                            </div>
                            <p class="feedback-text">Vehicle #TRK-045 was making loud noises during collection today. The exhaust seemed to be emitting more smoke than usual.</p>
                        </div>
                    </div>
                    
                    <div class="feedback-card">
                        <div class="feedback-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="feedback-content">
                            <div class="feedback-header">
                                <span class="feedback-user">James Pabila</span>
                                <span class="feedback-date">Jun 14, 2025</span>
                            </div>
                            <div class="feedback-rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="far fa-star"></i>
                            </div>
                            <p class="feedback-text">The hydraulic system on compactor #CMP-102 is getting slow. It takes longer to compact waste than before.</p>
                        </div>
                    </div>
                    
                    <h2 class="section-title" style="margin-top: 30px;"><i class="fas fa-user"></i> Driver Feedback</h2>
                    
                    <div class="feedback-card">
                        <div class="feedback-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="feedback-content">
                            <div class="feedback-header">
                                <span class="feedback-user">Anti Muni</span>
                                <span class="feedback-date">Jun 13, 2025</span>
                            </div>
                            <div class="feedback-rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                            <p class="feedback-text">Driver Michael was exceptionally courteous and careful when collecting bins today. He even helped me move a heavy bin to the curb!</p>
                        </div>
                    </div>
                    
                    <div class="feedback-card">
                        <div class="feedback-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="feedback-content">
                            <div class="feedback-header">
                                <span class="feedback-user">Green Community Center</span>
                                <span class="feedback-date">Jun 12, 2025</span>
                            </div>
                            <div class="feedback-rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="far fa-star"></i>
                                <i class="far fa-star"></i>
                            </div>
                            <p class="feedback-text">Driver #458 arrived 45 minutes late for our scheduled pickup today without notification. Please ensure punctuality for commercial clients.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Other Pages (Placeholder Content) -->
            <div class="page-content" id="usersPage">
                <h1 class="page-title"><i class="fas fa-users"></i> User Management</h1>
                <div class="card">
                    <div class="chart-container">
                        <canvas id="usersChart"></canvas>
                    </div>
                    <p style="margin-top: 20px; line-height: 1.7;">This section provides comprehensive tools for managing all registered users. Administrators can view user activity, assign roles, manage permissions, and handle user-related issues. Features include user search and filtering, activity logs, role-based access control, and communication tools.</p>
                </div>
            </div>
            
            <div class="page-content" id="messagesPage">
                <h1 class="page-title"><i class="fas fa-comments"></i> Messages</h1>
                <div class="card">
                    <div style="display: flex; height: 500px;">
                        <div style="width: 30%; border-right: 1px solid var(--light); padding-right: 20px;">
                            <h2 class="section-title"><i class="fas fa-inbox"></i> Conversations</h2>
                            <ul class="message-list">
                                <li class="message-item unread">
                                    <div class="message-header">
                                        <span class="message-sender">Sulley Ibrahim</span>
                                        <span class="message-time">Today</span>
                                    </div>
                                    <div class="message-preview">
                                        Bin collection issue
                                    </div>
                                </li>
                                <li class="message-item">
                                    <div class="message-header">
                                        <span class="message-sender">Green Apartments</span>
                                        <span class="message-time">Yesterday</span>
                                    </div>
                                    <div class="message-preview">
                                        Special collection request
                                    </div>
                                </li>
                                <li class="message-item">
                                    <div class="message-header">
                                        <span class="message-sender">City Council</span>
                                        <span class="message-time">Oct 14</span>
                                    </div>
                                    <div class="message-preview">
                                        Monthly meeting reminder
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div style="flex: 1; padding-left: 20px;">
                            <h2 class="section-title"><i class="fas fa-envelope"></i> New Message</h2>
                            <div class="form-group">
                                <input type="text" placeholder="Recipient">
                            </div>
                            <div class="form-group">
                                <input type="text" placeholder="Subject">
                            </div>
                            <div class="form-group">
                                <textarea rows="10" placeholder="Type your message here..."></textarea>
                            </div>
                            <button class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Send Message
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="page-content" id="collectionPage">
                <h1 class="page-title"><i class="fas fa-trash-alt"></i> Waste Collection</h1>
                <div class="card">
                    <div class="chart-container">
                        <canvas id="collectionChart"></canvas>
                    </div>
                    <p style="margin-top: 20px; line-height: 1.7;">This section provides tools for scheduling, monitoring, and managing waste collection operations. Features include route optimization, collection scheduling, real-time vehicle tracking, bin status monitoring, and collection reports. Administrators can assign vehicles to routes, track progress, and manage collection teams.</p>
                </div>
            </div>
            
            <div class="page-content" id="analyticsPage">
                <h1 class="page-title"><i class="fas fa-chart-line"></i> Analytics</h1>
                <div class="card">
                    <div class="chart-container">
                        <canvas id="analyticsChart"></canvas>
                    </div>
                    <p style="margin-top: 20px; line-height: 1.7;">The analytics dashboard provides comprehensive insights into waste management operations. Track key metrics like recycling rates, waste reduction progress, collection efficiency, carbon footprint reduction, and user engagement. Generate custom reports and visualize trends to make data-driven decisions for improving sustainability efforts.</p>
                </div>
            </div>
            
            <div class="page-content" id="vehiclesPage">
                <h1 class="page-title"><i class="fas fa-truck"></i> Vehicles</h1>
                <div class="card">
                    <div class="chart-container">
                        <canvas id="vehiclesChart"></canvas>
                    </div>
                    <p style="margin-top: 20px; line-height: 1.7;">Manage your waste collection fleet efficiently with this section. Track vehicle locations in real-time, monitor maintenance schedules, manage fuel consumption, assign drivers, and track operational costs. Features include maintenance alerts, route history, fuel efficiency reports, and driver performance metrics.</p>
                </div>
            </div>
            
            <div class="page-content" id="zonesPage">
                <h1 class="page-title"><i class="fas fa-map-marked-alt"></i> Zones</h1>
                <div class="card">
                    <div class="chart-container">
                        <canvas id="zonesChart"></canvas>
                    </div>
                    <p style="margin-top: 20px; line-height: 1.7;">This section allows administrators to define and manage collection zones. Optimize routes, set collection schedules by zone, analyze zone-specific metrics, and manage zone assignments. Features include interactive zone maps, collection frequency settings, performance comparisons between zones, and zone-specific reporting.</p>
                </div>
            </div>
            
            <div class="page-content" id="user-feedbackPage">
                <h1 class="page-title"><i class="fas fa-comment-dots"></i> User Feedback</h1>
                <div class="card">
                    <h2 class="section-title"><i class="fas fa-star"></i> Recent Feedback</h2>
                    
                    <div class="feedback-card">
                        <div class="feedback-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="feedback-content">
                            <div class="feedback-header">
                                <span class="feedback-user">Pongobu Guesthouse</span>
                                <span class="feedback-date">Jul 03, 2025</span>
                            </div>
                            <div class="feedback-rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                            </div>
                            <p class="feedback-text">The new recycling feature is fantastic! I've been able to reduce my household waste by 40% since I started using the app. The collection schedule reminders are very helpful.</p>
                        </div>
                    </div>
                    
                    <div class="feedback-card">
                        <div class="feedback-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="feedback-content">
                            <div class="feedback-header">
                                <span class="feedback-user">Green Community Center</span>
                                <span class="feedback-date">Jul 12, 2025</span>
                            </div>
                            <div class="feedback-rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                            <p class="feedback-text">We've been using EcoTrack for our community waste management and the results have been outstanding. The analytics dashboard helps us track our progress toward sustainability goals.</p>
                        </div>
                    </div>
                    
                    <div class="feedback-card">
                        <div class="feedback-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="feedback-content">
                            <div class="feedback-header">
                                <span class="feedback-user">Jennifer Seyram</span>
                                <span class="feedback-date">Jul 10, 2025</span>
                            </div>
                            <div class="feedback-rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="far fa-star"></i>
                            </div>
                            <p class="feedback-text">Overall a great app, but I wish the collection scheduling was more flexible. Sometimes the truck comes earlier than scheduled and misses my bins.</p>
                        </div>
                    </div>
                    
                    <div class="feedback-card">
                        <div class="feedback-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="feedback-content">
                            <div class="feedback-header">
                                <span class="feedback-user">Robert Abanga</span>
                                <span class="feedback-date">Jul 8, 2023</span>
                            </div>
                            <div class="feedback-rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                            </div>
                            <p class="feedback-text">The waste categorization guide has been super helpful. I'm now recycling correctly and have reduced contamination in my recycling bin significantly.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="page-content" id="settingsPage">
                <h1 class="page-title"><i class="fas fa-cog"></i> Settings</h1>
                <div class="card">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 25px;">
                        <div>
                            <h2 class="section-title"><i class="fas fa-user-cog"></i> Account Settings</h2>
                            <div class="form-group">
                                <label>Language</label>
                                <select>
                                    <option>English</option>
                                    <option>Spanish</option>
                                    <option>Portuguese</option>
                                    <option>German</option>
                                    <option>Italia</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Time Zone</label>
                                <select>
                                    <option>(GMT-05:00) Eastern Time</option>
                                    <option>(GMT-06:00) Central Time</option>
                                </select>
                            </div>
                        </div>
                        
                        <div>
                            <h2 class="section-title"><i class="fas fa-bell"></i> Notifications</h2>
                            <div class="form-group" style="display: flex; justify-content: space-between;">
                                <label>Email Notifications</label>
                                <label class="switch">
                                    <input type="checkbox" checked>
                                    <span class="slider"></span>
                                </label>
                            </div>
                            <div class="form-group" style="display: flex; justify-content: space-between;">
                                <label>Push Notifications</label>
                                <label class="switch">
                                    <input type="checkbox" checked>
                                    <span class="slider"></span>
                                </label>
                            </div>
                            <div class="form-group" style="display: flex; justify-content: space-between;">
                                <label>SMS Alerts</label>
                                <label class="switch">
                                    <input type="checkbox">
                                    <span class="slider"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div style="margin-top: 30px;">
                        <h2 class="section-title"><i class="fas fa-shield-alt"></i> Security</h2>
                        <button class="btn btn-primary">
                            <i class="fas fa-sync-alt"></i> Change Password
                        </button>
                        <button class="btn" style="background: #f8f9fa; margin-left: 15px;">
                            <i class="fas fa-lock"></i> Two-Factor Authentication
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <button class="mobile-menu-btn" id="mobileMenuBtn">
        <i class="fas fa-bars"></i>
    </button>

    <script>
        // Toggle sidebar
        const sidebar = document.querySelector('.sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
        });
        
        mobileMenuBtn.addEventListener('click', function() {
            sidebar.classList.toggle('active');
        });
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            if (window.innerWidth <= 992) {
                const isClickInsideSidebar = sidebar.contains(event.target);
                const isClickOnMobileBtn = mobileMenuBtn.contains(event.target);
                
                if (!isClickInsideSidebar && !isClickOnMobileBtn && sidebar.classList.contains('active')) {
                    sidebar.classList.remove('active');
                }
            }
        });
        
        // Profile picture upload
        const avatarInput = document.getElementById('avatarInput');
        const avatarPreview = document.getElementById('avatarPreview');
        const adminAvatar = document.getElementById('adminAvatar');
        const topbarAvatar = document.getElementById('topbarAvatar');
        
        document.getElementById('uploadBtn').addEventListener('click', function() {
            avatarInput.click();
        });
        
        avatarInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    avatarPreview.src = e.target.result;
                    adminAvatar.src = e.target.result;
                    topbarAvatar.src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });
        
        // Page navigation
        const menuItems = document.querySelectorAll('.menu-item');
        const pages = document.querySelectorAll('.page-content');
        
        menuItems.forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Remove active class from all items
                menuItems.forEach(i => i.classList.remove('active'));
                
                // Add active class to clicked item
                this.classList.add('active');
                
                // Get target page
                const targetPage = this.getAttribute('data-page');
                
                // Hide all pages
                pages.forEach(page => page.classList.remove('active'));
                
                // Show target page
                document.getElementById(`${targetPage}Page`).classList.add('active');
                
                // Close sidebar on mobile after selection
                if (window.innerWidth <= 992) {
                    sidebar.classList.remove('active');
                }
            });
        });
        
        // Logout functionality
        document.getElementById('logoutBtn').addEventListener('click', function() {
            if (confirm('Are you sure you want to logout?')) {
                alert('Logout successful! Redirecting to login page...');
                // In a real app, this would redirect to login page
            }
        });
        
        // Initialize avatar with default image
        const defaultAvatar = 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 24 24"><path fill="%232e7d32" d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4s-4 1.79-4 4s1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>';
        avatarPreview.src = defaultAvatar;
        adminAvatar.src = defaultAvatar;
        topbarAvatar.src = defaultAvatar;
        
        // Initialize charts
        document.addEventListener('DOMContentLoaded', function() {
            // Users Chart
            const usersCtx = document.getElementById('usersChart').getContext('2d');
            new Chart(usersCtx, {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct'],
                    datasets: [{
                        label: 'Active Users',
                        data: [3200, 3400, 3650, 3820, 4100, 4350, 4600, 4720, 4820, 4852],
                        backgroundColor: 'rgba(46, 125, 50, 0.7)',
                        borderColor: 'rgba(46, 125, 50, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
            
            // Collection Chart
            const collectionCtx = document.getElementById('collectionChart').getContext('2d');
            new Chart(collectionCtx, {
                type: 'line',
                data: {
                    labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                    datasets: [{
                        label: 'Waste Collected (kg)',
                        data: [1650, 1720, 1580, 1850, 1920, 1240, 980],
                        backgroundColor: 'rgba(255, 152, 0, 0.2)',
                        borderColor: 'rgba(255, 152, 0, 1)',
                        borderWidth: 3,
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
            
            // Analytics Chart
            const analyticsCtx = document.getElementById('analyticsChart').getContext('2d');
            new Chart(analyticsCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Recycled', 'Composted', 'Landfill', 'Hazardous'],
                    datasets: [{
                        data: [45, 25, 20, 10],
                        backgroundColor: [
                            'rgba(46, 125, 50, 0.8)',
                            'rgba(102, 187, 106, 0.8)',
                            'rgba(121, 85, 72, 0.8)',
                            'rgba(244, 67, 54, 0.8)'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right'
                        }
                    }
                }
            });
            
            // Vehicles Chart
            const vehiclesCtx = document.getElementById('vehiclesChart').getContext('2d');
            new Chart(vehiclesCtx, {
                type: 'polarArea',
                data: {
                    labels: ['Trucks', 'Vans', 'Compactors', 'Loaders'],
                    datasets: [{
                        data: [8, 3, 2, 2],
                        backgroundColor: [
                            'rgba(46, 125, 50, 0.7)',
                            'rgba(3, 169, 244, 0.7)',
                            'rgba(255, 152, 0, 0.7)',
                            'rgba(156, 39, 176, 0.7)'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
            
            // Zones Chart
            const zonesCtx = document.getElementById('zonesChart').getContext('2d');
            new Chart(zonesCtx, {
                type: 'radar',
                data: {
                    labels: ['Zone A', 'Zone B', 'Zone C', 'Zone D', 'Zone E'],
                    datasets: [{
                        label: 'Collection Efficiency',
                        data: [85, 75, 90, 65, 80],
                        backgroundColor: 'rgba(46, 125, 50, 0.2)',
                        borderColor: 'rgba(46, 125, 50, 1)',
                        pointBackgroundColor: 'rgba(46, 125, 50, 1)'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        r: {
                            angleLines: {
                                display: true
                            },
                            suggestedMin: 50,
                            suggestedMax: 100
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>
