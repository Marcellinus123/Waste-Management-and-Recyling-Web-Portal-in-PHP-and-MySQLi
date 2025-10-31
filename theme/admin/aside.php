<?php
$currentPage = basename($_SERVER['REQUEST_URI']);
?>

<aside class="app-sidebar">

    <ul class="app-menu">
        <li>
            <a class="app-menu__item <?= $currentPage == 'dashboard' ? 'active' : '' ?>" href="dashboard">
                <i class="app-menu__icon bi bi-speedometer"></i>
                <span class="app-menu__label">Dashboard</span>
            </a>
        </li>

        <!-- Payments -->
        <li class="treeview <?= $currentPage == 'payments' ? 'is-expanded' : '' ?>">
            <a class="app-menu__item" href="#" data-toggle="treeview">
                <i class="app-menu__icon bi bi-cash"></i>
                <span class="app-menu__label">Payments</span>
                <i class="treeview-indicator bi bi-chevron-right"></i>
            </a>
            <ul class="treeview-menu">
                <li><a class="treeview-item <?= $currentPage == 'payments' ? 'active' : '' ?>" href="payments"><i class="icon bi bi-circle-fill"></i> All payments</a></li>
            
            </ul>
        </li>

        <!-- Vehicle Booking -->
        <li>
            <a class="app-menu__item <?= $currentPage == 'ubookings' ? 'active' : '' ?>" href="ubookings">
                <i class="app-menu__icon bi bi-car-front-fill"></i>
                <span class="app-menu__label">Bookings</span>
            </a>
        </li>
         <li>
            <a class="app-menu__item <?= $currentPage == 'booking-agreement' ? 'active' : '' ?>" href="booking-agreement">
                <i class="app-menu__icon bi bi-car-front-fill"></i>
                <span class="app-menu__label">Endorse Booking</span>
            </a>
        </li>
        <li>
            <a class="app-menu__item <?= $currentPage == 'vehicles' ? 'active' : '' ?>" href="vehicles">
                <i class="app-menu__icon bi bi-car-front-fill"></i>
                <span class="app-menu__label">Vehicles</span>
            </a>
        </li>
        <li>
            <a class="app-menu__item <?= $currentPage == 'drivers' ? 'active' : '' ?>" href="drivers">
                <i class="app-menu__icon bi bi-car-front-fill"></i>
                <span class="app-menu__label">Drivers</span>
            </a>
        </li>
         <li>
            <a class="app-menu__item <?= $currentPage == 'users' ? 'active' : '' ?>" href="users">
                <i class="app-menu__icon bi bi-person-circle"></i>
                <span class="app-menu__label">Users</span>
            </a>
        </li>

        <!-- Support -->

        <!-- Feedback -->
       <li>
            <a class="app-menu__item <?= $currentPage == 'messages' ? 'active' : '' ?>" href="messages">
                <i class="app-menu__icon bi bi-chat-square-text-fill"></i>
                <span class="app-menu__label">Messages</span>
                <?php
                $unreadCount = 0;
                if (isset($_SESSION['user_id'])) {
                    try {
                        $stmt = $pdo->prepare("SELECT COUNT(*) as unread_count 
                                            FROM support_messages m
                                            JOIN support_tickets t ON m.ticket_id = t.ticket_id
                                            WHERE t.user_id = ? 
                                            AND m.sender_type = 'user' 
                                            AND m.is_read = FALSE");
                        $stmt->execute([$_SESSION['user_id']]);
                        $result = $stmt->fetch(PDO::FETCH_ASSOC);
                        $unreadCount = $result['unread_count'] ?? 0;
                    } catch (Exception $e) {
                        // Silently fail - don't break the menu if there's a DB error
                        error_log("Error fetching unread messages: " . $e->getMessage());
                    }
                }
                
                if ($unreadCount > 0): ?>
                <span class="badge bg-danger ms-2"><?= $unreadCount ?></span>
                <?php endif; ?>
            </a>
        </li>
        <!-- Apps -->
        <li class="treeview <?= in_array($currentPage, ['waste-tracking', 'recycling']) ? 'is-expanded' : '' ?>">
            <a class="app-menu__item" href="#" data-toggle="treeview">
                <i class="app-menu__icon bi bi-laptop"></i>
                <span class="app-menu__label">Web Management</span>
                <i class="treeview-indicator bi bi-chevron-right"></i>
            </a>
            <ul class="treeview-menu">
                <li><a class="treeview-item <?= $currentPage == 'about-page' ? 'active' : '' ?>" href="about-page">
                <i class="icon bi bi-question-circle"></i>About Page</a>
                </li>
                <li><a class="treeview-item <?= $currentPage == 'services-page' ? 'active' : '' ?>" href="services-page">
                <i class="icon bi bi-question-circle"></i> Services Page</a>
                </li>
                <li><a class="treeview-item <?= $currentPage == 'services-pricing' ? 'active' : '' ?>" href="services-pricing">
                <i class="icon bi bi-question-circle"></i> Services Pricing</a>
                </li>
                 <li><a class="treeview-item <?= $currentPage == 'feature-highlights' ? 'active' : '' ?>" href="feature-highlights">
                <i class="icon bi bi-question-circle"></i> Feature Highlights</a>
                </li>

                <li><a class="treeview-item <?= $currentPage == 'team-page' ? 'active' : '' ?>" href="team-page">
                <i class="icon bi bi-question-circle"></i> Team Page</a>
                </li>

                <li><a class="treeview-item <?= $currentPage == 'faqs-page' ? 'active' : '' ?>" href="faqs-page">
                <i class="icon bi bi-question-circle"></i> Faqs Page</a>
                </li>
                <li><a class="treeview-item <?= $currentPage == 'features-page' ? 'active' : '' ?>" href="features-page">
                <i class="icon bi bi-question-circle"></i> Features Page</a>
                </li>
                 <li><a class="treeview-item <?= $currentPage == 'feature-comparison' ? 'active' : '' ?>" href="feature-comparison">
                <i class="icon bi bi-question-circle"></i> Features Comparison</a>
                </li>
            </ul>
        </li>

        <!-- Profile -->
        <li>
            <a class="app-menu__item <?= $currentPage == 'contacts' ? 'active' : '' ?>" href="contacts">
                <i class="app-menu__icon bi bi-phone"></i>
                <span class="app-menu__label">Site Contacts</span>
            </a>
        </li>
        <li>
            <a class="app-menu__item <?= $currentPage == 'profile' ? 'active' : '' ?>" href="profile">
                <i class="app-menu__icon bi bi-person-circle"></i>
                <span class="app-menu__label">Profile</span>
            </a>
        </li>

        <!-- How Use -->
        <!--<li class="treeview <?= in_array($currentPage, ['table-basic', 'table-data-table']) ? 'is-expanded' : '' ?>">
            <a class="app-menu__item" href="#" data-toggle="treeview">
                <i class="app-menu__icon bi bi-table"></i>
                <span class="app-menu__label">How Use</span>
                <i class="treeview-indicator bi bi-chevron-right"></i>
            </a>
            <ul class="treeview-menu">
                <li><a class="treeview-item <?= $currentPage == 'table-basic' ? 'active' : '' ?>" href="table-basic"><i class="icon bi bi-circle-fill"></i> Website</a></li>
                <li><a class="treeview-item <?= $currentPage == 'table-data-table' ? 'active' : '' ?>" href="table-data-table"><i class="icon bi bi-circle-fill"></i> Portal</a></li>
            </ul>
        </li>-->
    </ul>
</aside>
