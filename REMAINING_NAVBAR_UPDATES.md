# Remaining Navbar Updates

## ✅ Completed
1. book-service-step1.php - Navbar updated
2. book-service-step2.php - Navbar updated

## 🔄 Remaining (Same Pattern)
3. book-service-step3.php
4. confirm-booking.php
5. book-custom-service.php

## Changes Needed for Each:

### HTML Change:
**Remove:**
```html
<a href="..." class="back-btn">
    <i class="fas fa-arrow-left"></i>
</a>
```

**Add:**
```html
<div class="user-section">
    <div class="header-icons">
        <a href="user-view-profile.php" class="header-icon">
            <i class="fas fa-user"></i>
        </a>
    </div>
</div>
```

### CSS Changes:
**Remove:** `.back-btn` and `.page-title` styles

**Add:**
```css
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

All pages will then match the home page navbar exactly!
