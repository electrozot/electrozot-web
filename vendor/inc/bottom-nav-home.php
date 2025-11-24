<!-- Bottom Navigation Bar for Home Pages -->
<div class="bottom-nav-home">
    <a href="index.php" class="bottom-nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>">
        <i class="fas fa-home"></i>
        <span>Home</span>
    </a>
    <a href="services.php" class="bottom-nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'services.php') ? 'active' : ''; ?>">
        <i class="fas fa-wrench"></i>
        <span>Services</span>
    </a>
    <a href="about.php" class="bottom-nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'about.php') ? 'active' : ''; ?>">
        <i class="fas fa-info-circle"></i>
        <span>About</span>
    </a>
    <a href="contact.php" class="bottom-nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'contact.php') ? 'active' : ''; ?>">
        <i class="fas fa-phone-alt"></i>
        <span>Contact</span>
    </a>
    <a href="usr/index.php" class="bottom-nav-item">
        <i class="fas fa-user"></i>
        <span>Login</span>
    </a>
</div>

<style>
    /* Bottom Navigation Bar */
    .bottom-nav-home {
        position: fixed;
        bottom: 0;
        left: 4px;
        right: 4px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        justify-content: space-around;
        align-items: center;
        padding: 3px 0 5px 0;
        box-shadow: 0 -4px 20px rgba(102, 126, 234, 0.4), 0 -2px 8px rgba(0,0,0,0.15);
        z-index: 9999;
        backdrop-filter: blur(15px);
        border: 2px solid rgba(255,255,255,0.2);
        border-bottom: none;
        border-radius: 20px 20px 15px 15px;
        position: relative;
        overflow: hidden;
    }
    
    .bottom-nav-home::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 100%;
        background: linear-gradient(180deg, rgba(255,255,255,0.1) 0%, transparent 100%);
        pointer-events: none;
    }

    .bottom-nav-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: rgba(255,255,255,0.75);
        text-decoration: none;
        padding: 4px 8px;
        border-radius: 10px;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        min-width: 48px;
        position: relative;
        z-index: 1;
    }

    .bottom-nav-item i {
        font-size: 1.05rem;
        margin-bottom: 1px;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));
    }

    .bottom-nav-item span {
        font-size: 0.58rem;
        font-weight: 600;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        letter-spacing: 0.3px;
        text-shadow: 0 1px 2px rgba(0,0,0,0.2);
    }

    .bottom-nav-item:hover {
        color: white;
        background: rgba(255,255,255,0.2);
        transform: translateY(-4px) scale(1.05);
        text-decoration: none;
        box-shadow: 0 4px 12px rgba(255,255,255,0.25);
    }

    .bottom-nav-item:hover i {
        transform: scale(1.25) rotate(5deg);
        filter: drop-shadow(0 3px 6px rgba(0,0,0,0.3));
    }

    .bottom-nav-item.active {
        color: white;
        background: rgba(255,255,255,0.25);
        box-shadow: 0 4px 15px rgba(255,255,255,0.3), inset 0 1px 3px rgba(255,255,255,0.2);
        border: 1px solid rgba(255,255,255,0.3);
    }

    .bottom-nav-item.active::before {
        content: '';
        position: absolute;
        top: -2px;
        left: 50%;
        transform: translateX(-50%);
        width: 28px;
        height: 3px;
        background: linear-gradient(90deg, transparent, white, transparent);
        border-radius: 0 0 3px 3px;
        box-shadow: 0 2px 8px rgba(255,255,255,0.6);
        animation: glow 2s ease-in-out infinite;
    }
    
    @keyframes glow {
        0%, 100% { opacity: 1; box-shadow: 0 2px 8px rgba(255,255,255,0.6); }
        50% { opacity: 0.7; box-shadow: 0 2px 12px rgba(255,255,255,0.8); }
    }

    .bottom-nav-item.active i {
        animation: bounce 0.6s ease-in-out;
        transform: scale(1.1);
    }

    @keyframes bounce {
        0%, 100% { transform: scale(1.1) translateY(0); }
        50% { transform: scale(1.15) translateY(-4px); }
    }

    /* Add padding to body to prevent content from being hidden behind bottom nav */
    body {
        padding-bottom: 60px;
    }

    /* Responsive adjustments */
    @media (max-width: 576px) {
        .bottom-nav-item {
            min-width: 44px;
            padding: 4px 6px;
        }

        .bottom-nav-item i {
            font-size: 1rem;
        }

        .bottom-nav-item span {
            font-size: 0.55rem;
        }
    }

    @media (min-width: 992px) {
        .bottom-nav-home {
            display: none; /* Hide on desktop, show only on mobile/tablet */
        }
        
        body {
            padding-bottom: 0 !important;
        }
    }
    
    /* Ensure bottom nav stays at bottom */
    @media (max-width: 991px) {
        .bottom-nav-home {
            position: fixed !important;
            bottom: 0 !important;
            left: 4px !important;
            right: 4px !important;
            margin: 0 !important;
        }
    }
</style>
