<?php
$currentPage = basename($_SERVER['REQUEST_URI']);
?>
<style>
    .active_page{
         border-bottom: 2px solid #4CAF50;
    }
</style>
<header>
    <div class="container header-container">
        <a href="homepage" class="logo">
            <div class="logo-icon">
                <i class="fas fa-recycle"></i>
            </div>
            <div class="logo-text">Waste <span>Wizard</span></div>
        </a>
        <nav class="nav-links">
            <a href="homepage" class="<?= $currentPage == 'homepage' ? 'active_page' : ''; ?>">Home</a>
            <a href="about" class="<?= $currentPage == 'about' ? 'active_page' : ''; ?>">About</a>
            <a href="features" class="<?= $currentPage == 'features' ? 'active_page' : ''; ?>">Features</a>
            <a href="how-it-works" class="<?= $currentPage == 'how-it-works' ? 'active_page' : ''; ?>">How It Works</a>
            <a href="pricing" class="<?= $currentPage == 'pricing' ? 'active_page' : ''; ?>">Pricing</a>
            <a href="contact" class="<?= $currentPage == 'contact' ? 'active_page' : ''; ?>">Contact</a>
        </nav>
        <div class="auth-buttons">
            <a href="userlogin" class="btn btn-outline">Login</a>
            <a href="usersignup" class="btn btn-solid">Sign Up</a>
        </div>
    </div>
</header>

