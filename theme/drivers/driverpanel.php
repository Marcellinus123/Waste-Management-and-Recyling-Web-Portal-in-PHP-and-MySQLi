<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WasteWizard - Driver Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        :root {
            --primary: #2e7d32;
            --primary-dark: #1b5e20;
            --secondary: #ff9800;
            --light: #f5f5f5;
            --dark: #333;
            --gray: #e0e0e0;
            --success: #4caf50;
            --warning: #ff9800;
            --danger: #f44336;
            --info: #2196f3;
            --sidebar-width: 260px;
            --sidebar-collapsed: 70px;
            --header-height: 80px;
        }

        body {
            background-color: #f0f2f5;
            color: var(--dark);
            overflow-x: hidden;
        }

        .container {
            display: flex;
            min-height: 100vh;
            transition: all 0.3s;
        }

        /* Sidebar Styles */
        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(180deg, var(--primary), var(--primary-dark));
            color: white;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            transition: all 0.3s;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 100;
        }

        .logo-container {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            display: flex;
            justify-content: center;
            align-items: center;
            height: var(--header-height);
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            display: flex;
            align-items: center;
            transition: all 0.3s;
        }

        .logo i {
            margin-right: 10px;
            color: var(--secondary);
            font-size: 28px;
        }

        .logo-text {
            transition: opacity 0.3s;
        }

        .nav-links {
            padding: 20px 0;
        }

        .nav-item {
            padding: 12px 20px;
            display: flex;
            align-items: center;
            cursor: pointer;
            transition: all 0.3s;
            border-left: 4px solid transparent;
        }

        .nav-item:hover, .nav-item.active {
            background: rgba(255,255,255,0.1);
            border-left: 4px solid var(--secondary);
        }

        .nav-item i {
            margin-right: 15px;
            font-size: 18px;
            width: 24px;
            text-align: center;
            transition: margin 0.3s;
        }

        .nav-text {
            transition: opacity 0.3s;
        }

        /* Toggle Button */
        .toggle-sidebar {
            position: absolute;
            top: 25px;
            right: -15px;
            background: var(--secondary);
            color: white;
            border: none;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            z-index: 101;
        }

        /* Main Content Styles */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            padding: 20px;
            transition: margin 0.3s;
            min-height: 100vh;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--gray);
            height: var(--header-height);
        }

        .page-title h1 {
            font-size: 24px;
            color: var(--dark);
        }

        .page-title p {
            color: #777;
        }

        .user-info {
            display: flex;
            align-items: center;
        }

        .user-info .notification {
            position: relative;
            margin-right: 20px;
            font-size: 20px;
            cursor: pointer;
        }

        .notification .badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: var(--danger);
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .user-profile {
            display: flex;
            align-items: center;
            cursor: pointer;
            position: relative;
        }

        .user-profile img {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 10px;
            border: 2px solid var(--primary);
        }

        /* Page Content */
        .page-content {
            display: none;
            animation: fadeIn 0.5s ease;
        }

        .active-page {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Stats Cards */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            transition: transform 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.1);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 24px;
        }

        .stat-card:nth-child(1) .stat-icon { background: rgba(76, 175, 80, 0.2); color: var(--success); }
        .stat-card:nth-child(2) .stat-icon { background: rgba(255, 152, 0, 0.2); color: var(--warning); }
        .stat-card:nth-child(3) .stat-icon { background: rgba(33, 150, 243, 0.2); color: var(--info); }
        .stat-card:nth-child(4) .stat-icon { background: rgba(244, 67, 54, 0.2); color: var(--danger); }

        .stat-info h3 {
            font-size: 24px;
            margin-bottom: 5px;
        }

        .stat-info p {
            color: #777;
            font-size: 14px;
        }

        /* Search and Filter */
        .search-filter {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }

        .search-container {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .search-box {
            flex: 1;
            min-width: 250px;
            position: relative;
        }

        .search-box input {
            width: 100%;
            padding: 12px 20px 12px 45px;
            border: 1px solid var(--gray);
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s;
        }

        .search-box input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.2);
            outline: none;
        }

        .search-box i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #777;
        }

        .filter-btn {
            padding: 12px 20px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            display: flex;
            align-items: center;
            transition: all 0.3s;
        }

        .filter-btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        .filter-btn i {
            margin-right: 8px;
        }

        /* Table Styles */
        .table-container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            overflow-x: auto;
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1000px;
        }

        thead {
            background-color: #f8f9fa;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        th {
            font-weight: 600;
            color: #555;
        }

        tbody tr:hover {
            background-color: #f9f9f9;
        }

        .status {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
            display: inline-block;
        }

        .status-pending { background: rgba(255, 152, 0, 0.2); color: var(--warning); }
        .status-in-progress { background: rgba(33, 150, 243, 0.2); color: var(--info); }
        .status-completed { background: rgba(76, 175, 80, 0.2); color: var(--success); }
        .status-cancelled { background: rgba(244, 67, 54, 0.2); color: var(--danger); }

        .action-btn {
            padding: 8px 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            margin-right: 5px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-view { background: #e3f2fd; color: #2196f3; }
        .btn-start { background: #e8f5e9; color: #4caf50; }
        .btn-complete { background: #fff3e0; color: #ff9800; }
        .btn-message { background: #f5f5f5; color: #333; }

        .action-btn:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }

        /* Messages Page */
        .messages-container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .message-card {
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 15px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .message-card:hover {
            background: #f9f9f9;
            border-color: var(--primary);
        }

        .message-card.unread {
            background: #e8f5e9;
            border-left: 4px solid var(--success);
        }

        .message-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .message-sender {
            font-weight: bold;
            color: var(--primary);
        }

        .message-time {
            color: #777;
            font-size: 14px;
        }

        .message-preview {
            color: #555;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Settings Page */
        .settings-container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }

        .profile-section {
            display: flex;
            gap: 30px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .profile-picture {
            text-align: center;
            flex: 1;
            min-width: 250px;
        }

        .profile-picture img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--primary);
            margin-bottom: 15px;
        }

        .profile-info {
            flex: 2;
            min-width: 300px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #555;
        }

        .form-group input, .form-group textarea, .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s;
        }

        .form-group input:focus, .form-group select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.2);
            outline: none;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-secondary {
            background: #e0e0e0;
            color: #333;
        }

        .btn:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }

        .file-upload {
            position: relative;
            display: inline-block;
        }

        .file-upload input[type="file"] {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        /* Reports Page */
        .reports-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .report-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            transition: all 0.3s;
        }

        .report-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.1);
        }

        .report-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .report-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 24px;
            background: rgba(46, 125, 50, 0.1);
            color: var(--primary);
        }

        .report-title {
            font-size: 18px;
            font-weight: 600;
        }

        .report-content {
            color: #555;
            margin-bottom: 15px;
        }

        /* Logout Page */
        .logout-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 60vh;
            text-align: center;
            background: white;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }

        .logout-icon {
            font-size: 80px;
            color: var(--primary);
            margin-bottom: 20px;
        }

        .logout-title {
            font-size: 28px;
            margin-bottom: 15px;
        }

        .logout-text {
            color: #777;
            margin-bottom: 30px;
            max-width: 500px;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: white;
            border-radius: 10px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            animation: modalFade 0.3s;
        }

        @keyframes modalFade {
            from { opacity: 0; transform: translateY(-50px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .modal-header {
            padding: 20px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h2 {
            font-size: 20px;
            color: var(--dark);
        }

        .close-modal {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #777;
        }

        .modal-body {
            padding: 20px;
        }

        .modal-footer {
            padding: 20px;
            border-top: 1px solid #eee;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .sidebar {
                width: var(--sidebar-collapsed);
            }
            
            .sidebar .logo-text, .sidebar .nav-text {
                opacity: 0;
                position: absolute;
            }
            
            .sidebar .logo i, .sidebar .nav-item i {
                margin-right: 0;
                font-size: 24px;
            }
            
            .sidebar .nav-item {
                justify-content: center;
                padding: 20px 0;
            }
            
            .main-content {
                margin-left: var(--sidebar-collapsed);
            }
        }

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
                height: auto;
            }
            
            .user-info {
                width: 100%;
                justify-content: space-between;
            }
            
            .stats-container {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            }
        }

        @media (max-width: 576px) {
            .sidebar {
                width: 0;
                overflow: hidden;
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .stats-container {
                grid-template-columns: 1fr;
            }
            
            .search-container {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="logo-container">
                <div class="logo">
                    <i class="fas fa-recycle"></i>
                    <span class="logo-text">Waste Wizard</span>
                </div>
            </div>
            
            <button class="toggle-sidebar" id="toggleSidebar">
                <i class="fas fa-chevron-left"></i>
            </button>
            
            <div class="nav-links">
                <div class="nav-item active" data-page="dashboard">
                    <i class="fas fa-tachometer-alt"></i>
                    <span class="nav-text">Dashboard</span>
                </div>
                <div class="nav-item" data-page="messages">
                    <i class="fas fa-comments"></i>
                    <span class="nav-text">Messages</span>
                </div>
                <div class="nav-item" data-page="reports">
                    <i class="fas fa-chart-line"></i>
                    <span class="nav-text">Reports</span>
                </div>
                <div class="nav-item" data-page="settings">
                    <i class="fas fa-cog"></i>
                    <span class="nav-text">Settings</span>
                </div>
                <div class="nav-item" data-page="logout">
                    <i class="fas fa-sign-out-alt"></i>
                    <span class="nav-text">Logout</span>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <div class="page-title">
                    <h1 id="currentPageTitle">Driver Dashboard</h1>
                    <p>Manage your bookings and trips</p>
                </div>
                
                <div class="user-info">
                    <div class="notification">
                        <i class="fas fa-bell"></i>
                        <span class="badge">3</span>
                    </div>
                    <div class="user-profile" id="profileBtn">
                        <img id="profileImg" src=" " alt="Driver">
                        <div>
                            <h4>Driver Profile</h4>
                            <p>Driver ID: #DRV-245</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Dashboard Page -->
            <div id="dashboard" class="page-content active-page">
                <!-- Stats Cards -->
                <div class="stats-container">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <div class="stat-info">
                            <h3>24</h3>
                            <p>Total Bookings</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-spinner"></i>
                        </div>
                        <div class="stat-info">
                            <h3>8</h3>
                            <p>In Progress</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-info">
                            <h3>14</h3>
                            <p>Completed</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-times-circle"></i>
                        </div>
                        <div class="stat-info">
                            <h3>2</h3>
                            <p>Cancelled</p>
                        </div>
                    </div>
                </div>
                
                <!-- Search and Filter -->
                <div class="search-filter">
                    <div class="search-container">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="searchInput" placeholder="Search bookings...">
                        </div>
                        
                        <button class="filter-btn">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                        
                        <button class="filter-btn" id="messageAdminBtn">
                            <i class="fas fa-comment"></i> Message Admin
                        </button>
                    </div>
                </div>
                
                <!-- Bookings Table -->
                <div class="table-container">
                    <table id="bookingsTable">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Booking Date</th>
                                <th>Ref Code</th>
                                <th>Location</th>
                                <th>Waste Type</th>
                                <th>Trip Detail</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Emmanuel Asare house</td>
                                <td>15 Oct 2023</td>
                                <td>REF-78945</td>
                                <td>123 Main St</td>
                                <td>Plastic</td>
                                <td>Residential Pickup</td>
                                <td><span class="status status-pending">Pending</span></td>
                                <td>
                                    <button class="action-btn btn-start">Start</button>
                                    <button class="action-btn btn-message">Message</button>
                                </td>
                            </tr>
                            <tr>
                                <td>God is Savior Sch.</td>
                                <td>16 Oct 2023</td>
                                <td>REF-78946</td>
                                <td>456 Oak Ave</td>
                                <td>Organic</td>
                                <td>Commercial Collection</td>
                                <td><span class="status status-in-progress">In Progress</span></td>
                                <td>
                                    <button class="action-btn btn-complete">Complete</button>
                                    <button class="action-btn btn-message">Message</button>
                                </td>
                            </tr>
                            <tr>
                                <td>Michael Ofosu</td>
                                <td>17 Oct 2023</td>
                                <td>REF-78947</td>
                                <td>789 Pine Rd</td>
                                <td>Electronic</td>
                                <td>Special Handling</td>
                                <td><span class="status status-completed">Completed</span></td>
                                <td>
                                    <button class="action-btn btn-view">View</button>
                                    <button class="action-btn btn-message">Message</button>
                                </td>
                            </tr>
                            <tr>
                                <td>CKT-UTAS</td>
                                <td>18 Oct 2023</td>
                                <td>REF-78948</td>
                                <td>321 Elm St</td>
                                <td>Paper</td>
                                <td>Office Collection</td>
                                <td><span class="status status-pending">Pending</span></td>
                                <td>
                                    <button class="action-btn btn-start">Start</button>
                                    <button class="action-btn btn-message">Message</button>
                                </td>
                            </tr>
                            <tr>
                                <td>Navasco</td>
                                <td>19 Oct 2023</td>
                                <td>REF-78949</td>
                                <td>654 Birch Ln</td>
                                <td>Glass</td>
                                <td>Recycling Center</td>
                                <td><span class="status status-in-progress">In Progress</span></td>
                                <td>
                                    <button class="action-btn btn-complete">Complete</button>
                                    <button class="action-btn btn-message">Message</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Messages Page -->
            <div id="messages" class="page-content">
                <div class="search-filter">
                    <div class="search-container">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" placeholder="Search messages...">
                        </div>
                        
                        <button class="filter-btn">
                            <i class="fas fa-plus"></i> New Message
                        </button>
                    </div>
                </div>
                
                <div class="messages-container">
                    <div class="message-card unread">
                        <div class="message-header">
                            <div class="message-sender">Admin Team</div>
                            <div class="message-time">Today, 09:15 AM</div>
                        </div>
                        <div class="message-subject">Schedule Change for Friday</div>
                        <div class="message-preview">Please note that your pickup schedule for Friday has been adjusted due to a city event. The new time is...</div>
                    </div>
                    
                    <div class="message-card">
                        <div class="message-header">
                            <div class="message-sender">Dispatch Manager</div>
                            <div class="message-time">Yesterday, 02:30 PM</div>
                        </div>
                        <div class="message-subject">New Route Assignment</div>
                        <div class="message-preview">You've been assigned to a new route starting next week. Please review the details in your schedule...</div>
                    </div>
                    
                    <div class="message-card">
                        <div class="message-header">
                            <div class="message-sender">Support Team</div>
                            <div class="message-time">Oct 15, 2023</div>
                        </div>
                        <div class="message-subject">Vehicle Maintenance Reminder</div>
                        <div class="message-preview">This is a reminder that your assigned vehicle is due for routine maintenance. Please schedule...</div>
                    </div>
                    
                    <div class="message-card">
                        <div class="message-header">
                            <div class="message-sender">Operations Director</div>
                            <div class="message-time">Oct 12, 2023</div>
                        </div>
                        <div class="message-subject">Monthly Performance Report</div>
                        <div class="message-preview">Your monthly performance report is now available. You can view it in the Reports section of your...</div>
                    </div>
                </div>
            </div>
            
            <!-- Reports Page -->
            <div id="reports" class="page-content">
                <div class="page-title">
                    <h1>Performance Reports</h1>
                    <p>View your monthly statistics and performance metrics</p>
                </div>
                
                <div class="reports-container">
                    <div class="report-card">
                        <div class="report-header">
                            <div class="report-icon">
                                <i class="fas fa-chart-bar"></i>
                            </div>
                            <div class="report-title">Monthly Collection Report</div>
                        </div>
                        <div class="report-content">
                            Detailed breakdown of waste collected by type and location for the current month.
                        </div>
                        <button class="btn btn-primary">View Report</button>
                    </div>
                    
                    <div class="report-card">
                        <div class="report-header">
                            <div class="report-icon">
                                <i class="fas fa-route"></i>
                            </div>
                            <div class="report-title">Route Efficiency</div>
                        </div>
                        <div class="report-content">
                            Analysis of your route efficiency, time management, and fuel consumption.
                        </div>
                        <button class="btn btn-primary">View Report</button>
                    </div>
                    
                    <div class="report-card">
                        <div class="report-header">
                            <div class="report-icon">
                                <i class="fas fa-star"></i>
                            </div>
                            <div class="report-title">Customer Feedback</div>
                        </div>
                        <div class="report-content">
                            Summary of customer ratings and feedback received for your services.
                        </div>
                        <button class="btn btn-primary">View Report</button>
                    </div>
                    
                    <div class="report-card">
                        <div class="report-header">
                            <div class="report-icon">
                                <i class="fas fa-calendar"></i>
                            </div>
                            <div class="report-title">Schedule Compliance</div>
                        </div>
                        <div class="report-content">
                            Report showing your on-time performance and schedule adherence metrics.
                        </div>
                        <button class="btn btn-primary">View Report</button>
                    </div>
                </div>
            </div>
            
            <!-- Settings Page -->
            <div id="settings" class="page-content">
                <div class="page-title">
                    <h1>Account Settings</h1>
                    <p>Update your profile and preferences</p>
                </div>
                
                <div class="settings-container">
                    <div class="profile-section">
                        <div class="profile-picture">
                            <img id="profileDisplay" src=" " alt="Driver Profile">
                            <div class="file-upload">
                                <button class="btn btn-primary">
                                    <i class="fas fa-upload"></i> Change Profile Photo
                                </button>
                                <input type="file" id="profileUpload" accept="image/*">
                            </div>
                        </div>
                        
                        <div class="profile-info">
                            <div class="form-group">
                                <label for="fullName">Full Name</label>
                                <input type="text" id="fullName" value="Driver">
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <input type="email" id="email" value="driver@wastewise.com">
                            </div>
                            
                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input type="tel" id="phone" value="+233537078423">
                            </div>
                            
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" id="password" value="••••••••">
                            </div>
                            
                            <button class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Vehicle Information</label>
                        <div class="search-container">
                            <div class="search-box" style="flex: 2;">
                                <input type="text" placeholder="Vehicle Model" value="Ford F-550">
                            </div>
                            <div class="search-box" style="flex: 1;">
                                <input type="text" placeholder="License Plate" value="WM-245">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="notifications">Notification Preferences</label>
                        <select id="notifications">
                            <option>Email & App Notifications</option>
                            <option>Email Only</option>
                            <option>App Only</option>
                            <option>No Notifications</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- Logout Page -->
            <div id="logout" class="page-content">
                <div class="logout-container">
                    <div class="logout-icon">
                        <i class="fas fa-sign-out-alt"></i>
                    </div>
                    <h2 class="logout-title">Log Out of Your Account</h2>
                    <p class="logout-text">Are you sure you want to log out? You'll need to sign in again to access your driver panel and bookings.</p>
                    <button class="btn btn-primary" style="margin-right: 10px;">
                        <i class="fas fa-sign-in-alt"></i> Cancel
                    </button>
                    <button class="btn btn-secondary" id="logoutBtn">
                        <i class="fas fa-sign-out-alt"></i> Log Out
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Message Admin Modal -->
    <div class="modal" id="messageModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Message Admin</h2>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="subject">Subject</label>
                    <input type="text" id="subject" placeholder="Enter subject">
                </div>
                <div class="form-group">
                    <label for="bookingRef">Booking Reference</label>
                    <select id="bookingRef">
                        <option value="">Select booking</option>
                        <option value="REF-78945">REF-78945 - Robert Johnson</option>
                        <option value="REF-78946">REF-78946 - Sarah Williams</option>
                        <option value="REF-78947">REF-78947 - Michael Brown</option>
                        <option value="REF-78948">REF-78948 - Emma Davis</option>
                        <option value="REF-78949">REF-78949 - James Wilson</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea id="message" placeholder="Type your message to admin"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary close-modal">Cancel</button>
                <button class="btn btn-primary" id="sendMessageBtn">Send Message</button>
            </div>
        </div>
    </div>
    
    <script>
        // DOM Elements
        const sidebar = document.getElementById('sidebar');
        const toggleSidebar = document.getElementById('toggleSidebar');
        const navItems = document.querySelectorAll('.nav-item');
        const pageContents = document.querySelectorAll('.page-content');
        const currentPageTitle = document.getElementById('currentPageTitle');
        const searchInput = document.getElementById('searchInput');
        const bookingsTable = document.getElementById('bookingsTable');
        const messageModal = document.getElementById('messageModal');
        const messageAdminBtn = document.getElementById('messageAdminBtn');
        const closeModalBtns = document.querySelectorAll('.close-modal');
        const sendMessageBtn = document.getElementById('sendMessageBtn');
        const profileUpload = document.getElementById('profileUpload');
        const profileDisplay = document.getElementById('profileDisplay');
        const logoutBtn = document.getElementById('logoutBtn');

        // Toggle sidebar
        toggleSidebar.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            const isCollapsed = sidebar.classList.contains('collapsed');
            
            if (isCollapsed) {
                sidebar.style.width = 'var(--sidebar-collapsed)';
                document.querySelector('.main-content').style.marginLeft = 'var(--sidebar-collapsed)';
                toggleSidebar.innerHTML = '<i class="fas fa-chevron-right"></i>';
            } else {
                sidebar.style.width = 'var(--sidebar-width)';
                document.querySelector('.main-content').style.marginLeft = 'var(--sidebar-width)';
                toggleSidebar.innerHTML = '<i class="fas fa-chevron-left"></i>';
            }
        });

        // Switch pages
        navItems.forEach(item => {
            item.addEventListener('click', function() {
                const pageId = this.getAttribute('data-page');
                
                // Update active nav item
                navItems.forEach(nav => nav.classList.remove('active'));
                this.classList.add('active');
                
                // Show selected page
                pageContents.forEach(page => page.classList.remove('active-page'));
                document.getElementById(pageId).classList.add('active-page');
                
                // Update page title
                currentPageTitle.textContent = this.querySelector('.nav-text').textContent;
            });
        });

        // Search functionality
        searchInput.addEventListener('keyup', function() {
            const searchTerm = searchInput.value.toLowerCase();
            const rows = bookingsTable.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
            
            for (let i = 0; i < rows.length; i++) {
                const cells = rows[i].getElementsByTagName('td');
                let found = false;
                
                for (let j = 0; j < cells.length; j++) {
                    const cellText = cells[j].textContent.toLowerCase();
                    if (cellText.includes(searchTerm)) {
                        found = true;
                        break;
                    }
                }
                
                rows[i].style.display = found ? '' : 'none';
            }
        });

        // Modal functionality
        messageAdminBtn.addEventListener('click', function() {
            messageModal.style.display = 'flex';
        });

        closeModalBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                messageModal.style.display = 'none';
            });
        });

        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            if (event.target === messageModal) {
                messageModal.style.display = 'none';
            }
        });

        // Send message functionality
        sendMessageBtn.addEventListener('click', function() {
            const subject = document.getElementById('subject').value;
            const bookingRef = document.getElementById('bookingRef').value;
            const message = document.getElementById('message').value;
            
            if (!subject || !bookingRef || !message) {
                alert('Please fill in all fields');
                return;
            }
            
            alert(`Message sent to admin!\nSubject: ${subject}\nBooking: ${bookingRef}\nMessage: ${message}`);
            messageModal.style.display = 'none';
            
            // Reset form
            document.getElementById('subject').value = '';
            document.getElementById('bookingRef').value = '';
            document.getElementById('message').value = '';
        });

        // Action button functionality
        document.querySelectorAll('.action-btn').forEach(button => {
            button.addEventListener('click', function() {
                const row = this.closest('tr');
                const userName = row.cells[0].textContent;
                const refCode = row.cells[2].textContent;
                
                if (this.classList.contains('btn-start')) {
                    this.textContent = 'In Progress';
                    this.classList.remove('btn-start');
                    this.classList.add('btn-complete');
                    row.cells[6].innerHTML = '<span class="status status-in-progress">In Progress</span>';
                    alert(`Trip started for ${userName} (${refCode})`);
                } else if (this.classList.contains('btn-complete')) {
                    row.cells[6].innerHTML = '<span class="status status-completed">Completed</span>';
                    this.textContent = 'Completed';
                    this.classList.remove('btn-complete');
                    this.classList.add('btn-view');
                    alert(`Trip completed for ${userName} (${refCode})`);
                } else if (this.classList.contains('btn-message')) {
                    messageModal.style.display = 'flex';
                    document.getElementById('bookingRef').value = refCode;
                }
            });
        });

        // Profile image upload
        profileUpload.addEventListener('change', function(e) {
            if (e.target.files && e.target.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(event) {
                    profileDisplay.src = event.target.result;
                    document.getElementById('profileImg').src = event.target.result;
                }
                
                reader.readAsDataURL(e.target.files[0]);
            }
        });

        // Logout functionality
        logoutBtn.addEventListener('click', function() {
            if (confirm('Are you sure you want to log out?')) {
                alert('You have been logged out successfully.');
                // In a real app, this would redirect to login page
            }
        });

        // Simulate page loading
        setTimeout(() => {
            document.querySelector('.container').style.opacity = 1;
        }, 300);
    </script>
</body>
</html>
