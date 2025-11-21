# Standard Navigation Template

## Bottom Navigation CSS (Rounded Edges)

```css
.bottom-nav {
    position: fixed;
    bottom: 8px;
    left: 8px;
    right: 8px;
    background: white;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    display: flex;
    justify-content: space-around;
    padding: 6px 0;
    z-index: 1000;
    border-radius: 20px;
}

.nav-item {
    flex: 1;
    text-align: center;
    text-decoration: none;
    color: #999;
    transition: all 0.3s;
    padding: 4px;
}

.nav-item.active { color: #667eea; }

.nav-item i {
    font-size: 20px;
    display: block;
    margin-bottom: 3px;
}

.nav-item span {
    font-size: 10px;
    font-weight: 600;
}
```

## Top Navbar CSS (No Back Button)

```css
.top-header {
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #d946ef 100%);
    color: white;
    padding: 10px 15px;
    box-shadow: 0 4px 20px rgba(99, 102, 241, 0.3);
}

.header-content {
    display: flex;
    align-items: center;
    gap: 15px;
}

.brand-section {
    display: flex;
    align-items: center;
    gap: 15px;
}

.logo {
    height: 55px;
    width: auto;
}

.brand-text h2 {
    font-size: 24px;
    font-weight: 700;
    margin: 0;
    line-height: 1.2;
}

.brand-text p {
    font-size: 13px;
    opacity: 0.85;
    margin: 3px 0 0 0;
    font-style: italic;
}

.user-section {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-left: auto;
}

.header-icons {
    display: flex;
    gap: 6px;
}

.header-icon {
    width: 32px;
    height: 32px;
    background: rgba(255,255,255,0.25);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    text-decoration: none;
    font-size: 14px;
}
```

## Top Navbar HTML

```html
<div class="top-header">
    <div class="header-content">
        <div class="brand-section">
            <img src="../vendor/EZlogonew.png" alt="Electrozot" class="logo">
            <div class="brand-text">
                <h2>Electrozot</h2>
                <p>We make perfect</p>
            </div>
        </div>
        <div class="user-section">
            <div class="header-icons">
                <a href="user-view-profile.php" class="header-icon">
                    <i class="fas fa-user"></i>
                </a>
            </div>
        </div>
    </div>
</div>
```

## Pages to Update

1. user-manage-booking.php ✅ DONE
2. user-view-profile.php - TODO
3. user-give-feedback.php - TODO  
4. book-service-step1.php - TODO
5. book-service-step2.php - TODO
6. book-service-step3.php - TODO
7. confirm-booking.php - TODO
8. book-custom-service.php - TODO
