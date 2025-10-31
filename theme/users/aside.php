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
            <a class="app-menu__item <?= $currentPage == 'vehicle-booking' ? 'active' : '' ?>" href="vehicle-booking">
                <i class="app-menu__icon bi bi-car-front-fill"></i>
                <span class="app-menu__label">Vehicle Booking</span>
            </a>
        </li>
          <li>
            <a class="app-menu__item <?= $currentPage == 'booking-agreement' ? 'active' : '' ?>" href="booking-agreement">
                <i class="app-menu__icon bi bi-car-front-fill"></i>
                <span class="app-menu__label">Endorse Booking</span>
            </a>
        </li>


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
                                            AND m.sender_type = 'admin' 
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
                <span class="app-menu__label">Apps</span>
                <i class="treeview-indicator bi bi-chevron-right"></i>
            </a>
            <ul class="treeview-menu">
                <li><a class="treeview-item <?= $currentPage == 'waste-tracking' ? 'active' : '' ?>" href="waste-tracking">
                    <i class="icon bi bi-trash"></i> Waste Tracking</a>
                </li>      
            </ul>        
        </li>

        <!-- Profile -->
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
