<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    
    body {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        background: linear-gradient(135deg, #f5f7ff 0%, #e8f4f8 100%);
        padding-bottom: 65px;
        min-height: 100vh;
    }
    
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
    
    .user-name {
        font-size: 16px;
        font-weight: 600;
        white-space: nowrap;
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
        font-size: 14px;
        text-decoration: none;
        color: white;
        transition: all 0.3s;
    }
    
    .header-icon:hover {
        background: rgba(255,255,255,0.35);
        transform: scale(1.05);
    }
    
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
    
    /* Tablet & Desktop Responsive */
    @media (min-width: 768px) {
        body {
            background: #e9ecef;
        }
        
        .top-header {
            padding: 30px 20px 35px;
        }
        
        .logo {
            height: 55px;
        }
        
        .brand-text h2 {
            font-size: 24px;
        }
        
        .brand-text p {
            font-size: 13px;
        }
        
        .user-name {
            font-size: 18px;
        }
        
        .header-icon {
            width: 42px;
            height: 42px;
            font-size: 18px;
        }
        
        .bottom-nav {
            max-width: 1200px;
            left: 50%;
            transform: translateX(-50%);
            bottom: 10px;
            margin: 0 10px;
            border-radius: 20px;
        }
    }
</style>
