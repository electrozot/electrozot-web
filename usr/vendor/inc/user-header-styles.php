<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    
    body {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding-bottom: 65px;
        min-height: 100vh;
    }
    
    .top-header {
        background: linear-gradient(135deg, #f9a8a8 0%, #f59e9e 20%, #f48fb1 50%, #ec6ead 80%, #d13abd 100%);
        color: white;
        padding: 10px 15px;
        box-shadow: 0 4px 20px rgba(209, 58, 189, 0.3);
    }
    
    .header-content {
        display: flex;
        align-items: center;
        gap: 15px;
        padding-left: 0;
        margin-left: -5px;
    }
    
    .brand-section {
        display: flex;
        align-items: center;
        gap: 4px;
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
        left: 50%;
        transform: translateX(-50%);
        width: calc(100% - 16px);
        max-width: 450px;
        background: linear-gradient(135deg, #f9a8a8 0%, #f59e9e 20%, #f48fb1 50%, #ec6ead 80%, #d13abd 100%);
        box-shadow: 0 3px 20px rgba(209, 58, 189, 0.35), 0 1px 5px rgba(0,0,0,0.1);
        display: flex;
        justify-content: space-around;
        padding: 4px 6px;
        z-index: 1000;
        border-radius: 20px;
    }
    
    .nav-item {
        flex: 1;
        text-align: center;
        text-decoration: none;
        color: rgba(255, 255, 255, 0.75);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        padding: 4px 2px;
        position: relative;
        border-radius: 12px;
    }
    
    .nav-item:hover {
        color: white;
        background: rgba(255, 255, 255, 0.15);
        transform: translateY(-1px);
    }
    
    .nav-item.active { 
        color: white;
        background: rgba(255, 255, 255, 0.25);
        box-shadow: 0 1px 5px rgba(0, 0, 0, 0.2);
    }
    
    .nav-item i {
        font-size: 16px;
        display: block;
        margin-bottom: 1px;
    }
    
    .nav-item.active i {
        animation: bounce 0.4s ease;
    }
    
    @keyframes bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-3px); }
    }
    
    .nav-item span {
        font-size: 8px;
        font-weight: 600;
        letter-spacing: 0.2px;
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
            max-width: 400px;
            bottom: 10px;
            padding: 5px 8px;
        }
        
        .nav-item {
            padding: 5px 4px;
        }
        
        .nav-item i {
            font-size: 18px;
            margin-bottom: 2px;
        }
        
        .nav-item span {
            font-size: 9px;
        }
    }
</style>
