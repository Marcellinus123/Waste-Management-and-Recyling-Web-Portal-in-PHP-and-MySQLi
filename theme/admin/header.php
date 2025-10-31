<header class="app-header"><a class="app-header__logo" href="index">WWP</a>
<!-- Sidebar toggle button--><a class="app-sidebar__toggle" href="#" data-toggle="sidebar" aria-label="Hide Sidebar"></a>
<!-- Navbar Right Menu-->
<ul class="app-nav">
    <!--Notification Menu-->
    <li class="dropdown">
        <a class="app-nav__item" href="#" data-bs-toggle="dropdown" aria-label="Show messages">
            <i class="bi bi-chat-square-text fs-5"></i>
            <span id="message-badge" class="notification-badge"></span>
        </a>
        <ul class="app-notification dropdown-menu dropdown-menu-right">
            <li class="app-notification__title">You have <span id="message-count">0</span> new messages</li>
            <div class="app-notification__content" id="message-notifications">
                <!-- Messages will appear here dynamically -->
            </div>
            <li class="app-notification__footer"><a href="messages">View all messages</a></li>
        </ul>
    </li>
    <!-- User Menu-->
    <li class="dropdown"><a class="app-nav__item" href="#" data-bs-toggle="dropdown" aria-label="Open Profile Menu"><i class="bi bi-person fs-4"></i></a>
    <ul class="dropdown-menu settings-menu dropdown-menu-right">
        <li><a class="dropdown-item" href="profile"><i class="bi bi-gear me-2 fs-5"></i> Settings</a></li>
        <li><a class="dropdown-item" href="logout"><i class="bi bi-box-arrow-right me-2 fs-5"></i> Logout</a></li>
    </ul>
    </li>
</ul>
</header>
<style>
    .notification-badge {
    display: none;
    position: absolute;
    top: -1px;
    right: -2px;
    background-color: #dc3545;
    color: white;
    border-radius: 50%;
    width: 18px;
    height: 18px;
    font-size: 8px;
    text-align: center;
    line-height: 18px;
}

.app-nav__item {
    position: relative;
    display: inline-block;
}
</style>
<script>
    // Initialize WebSocket connection
let messageSocket;
let reconnectAttempts = 0;
const maxReconnectAttempts = 5;
function connectMessageSocket() {
    messageSocket = new WebSocket(`ws://localhost:8082?userId=${encodeURIComponent('<?= $_SESSION['user_id'] ?>')}`);

    messageSocket.onopen = function(e) {
        console.log("Message WebSocket connection established");
        reconnectAttempts = 0;
    };

    messageSocket.onmessage = function(event) {
        const data = JSON.parse(event.data);
        
        if (data.type === 'new_message') {
            // Update badge count
            const badge = document.getElementById('message-badge');
            const currentCount = parseInt(badge.textContent) || 0;
            badge.textContent = currentCount + 1;
            badge.style.display = 'inline-block';
            
            // Update notification count
            const countElement = document.getElementById('message-count');
            countElement.textContent = parseInt(countElement.textContent) + 1;
            
            // Add new message to dropdown
            const message = data.data;
            const notificationsContainer = document.getElementById('message-notifications');
            const newNotification = document.createElement('li');
            newNotification.innerHTML = `
                <a class="app-notification__item" href="messages?ticket_id=${message.ticket_id}">
                    <span class="app-notification__icon"><i class="bi bi-envelope fs-4 text-primary"></i></span>
                    <div>
                        <p class="app-notification__message">New message in ticket #${message.ticket_id}</p>
                        <p class="app-notification__meta">${formatTime(message.created_at)}</p>
                    </div>
                </a>
            `;
            notificationsContainer.insertBefore(newNotification, notificationsContainer.firstChild);
            
            // Play notification sound
            playNotificationSound();
        }
    };

    messageSocket.onclose = function(e) {
        if (reconnectAttempts < maxReconnectAttempts) {
            console.log(`WebSocket connection closed. Attempting to reconnect (${reconnectAttempts + 1}/${maxReconnectAttempts})...`);
            setTimeout(connectMessageSocket, Math.min(1000 * (reconnectAttempts + 1), 5000));
            reconnectAttempts++;
        } else {
            console.log("Max reconnection attempts reached");
        }
    };

    messageSocket.onerror = function(err) {
        console.error("WebSocket error:", err);
    };
}

// Helper function to format time
function formatTime(timestamp) {
    const now = new Date();
    const messageTime = new Date(timestamp);
    const diffMinutes = Math.floor((now - messageTime) / (1000 * 60));
    
    if (diffMinutes < 1) return "Just now";
    if (diffMinutes < 60) return `${diffMinutes} min ago`;
    if (diffMinutes < 1440) return `${Math.floor(diffMinutes / 60)} hours ago`;
    return `${Math.floor(diffMinutes / 1440)} days ago`;
}

// Play notification sound
function playNotificationSound() {
    const audio = new Audio('../assets/sound/message-notification.mp3');
    audio.volume = 0.3;
    audio.play().catch(e => console.log("Audio playback prevented:", e));
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    connectMessageSocket();
    
    // Load initial unread count
    fetch('get_unread_messages.php')
        .then(response => response.json())
        .then(data => {
            const badge = document.getElementById('message-badge');
            const countElement = document.getElementById('message-count');
            
            if (data.count > 0) {
                badge.textContent = data.count;
                badge.style.display = 'inline-block';
                countElement.textContent = data.count;
            }
        });
});
</script>