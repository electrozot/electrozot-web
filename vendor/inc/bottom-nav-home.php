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
        background: #ffffff !important;
        display: flex;
        justify-content: space-around;
        align-items: center;
        padding: 2px 0 3px 0;
        box-shadow: 0 -2px 8px rgba(0, 0, 0, 0.15);
        z-index: 99999 !important;
        border: 2px solid #dc143c !important;
        border-radius: 20px;
        height: 42px;
    }

    .bottom-nav-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: #6c757d !important;
        text-decoration: none;
        padding: 2px 6px;
        border-radius: 6px;
        min-width: 42px;
    }

    .bottom-nav-item i {
        font-size: 0.95rem;
        margin-bottom: 1px;
        color: inherit !important;
    }

    .bottom-nav-item span {
        font-size: 0.52rem;
        font-weight: 600;
        letter-spacing: 0.2px;
        color: inherit !important;
    }

    .bottom-nav-item:hover {
        color: #495057 !important;
        background: rgba(108, 117, 125, 0.1);
        text-decoration: none;
    }

    .bottom-nav-item.active {
        color: #212529 !important;
        background: rgba(33, 37, 41, 0.1);
    }

    /* Prevent any red colors from other CSS - More specific selectors */
    .bottom-nav-home .bottom-nav-item:visited,
    .bottom-nav-home .bottom-nav-item:focus,
    .bottom-nav-home .bottom-nav-item:active:not(.active),
    .bottom-nav-home .bottom-nav-item:link {
        color: #6c757d !important;
        text-decoration: none !important;
    }

    .bottom-nav-home .bottom-nav-item.active:visited,
    .bottom-nav-home .bottom-nav-item.active:focus,
    .bottom-nav-home .bottom-nav-item.active:link {
        color: #212529 !important;
        text-decoration: none !important;
    }

    /* Override any Bootstrap or other framework link colors */
    .bottom-nav-home a,
    .bottom-nav-home a:hover,
    .bottom-nav-home a:focus,
    .bottom-nav-home a:active,
    .bottom-nav-home a:visited {
        color: #6c757d !important;
        text-decoration: none !important;
    }

    .bottom-nav-home a.active,
    .bottom-nav-home a.active:hover,
    .bottom-nav-home a.active:focus,
    .bottom-nav-home a.active:visited {
        color: #212529 !important;
        text-decoration: none !important;
    }

    /* Add padding to body to prevent content from being hidden behind bottom nav */
    body {
        padding-bottom: 42px;
    }

    /* Responsive adjustments */
    @media (max-width: 576px) {
        .bottom-nav-item {
            min-width: 40px;
            padding: 2px 5px;
        }

        .bottom-nav-item i {
            font-size: 0.9rem;
        }

        .bottom-nav-item span {
            font-size: 0.5rem;
        }
        
        .bottom-nav-home {
            height: 40px;
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
            display: flex !important;
            position: fixed !important;
            bottom: 0 !important;
            left: 4px !important;
            right: 4px !important;
            margin: 0 !important;
            z-index: 99999 !important;
        }
    }
    
    /* Force visibility on mobile */
    @media (max-width: 767px) {
        .bottom-nav-home {
            display: flex !important;
            visibility: visible !important;
            opacity: 1 !important;
        }
    }
</style>
