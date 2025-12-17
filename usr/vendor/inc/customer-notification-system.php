<!-- Customer Notification System -->
<style>
.notification-toast {
    position: fixed;
    top: 80px;
    right: 20px;
    min-width: 300px;
    max-width: 400px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
    padding: 15px;
    z-index: 9999;
    animation: slideInRight 0.3s ease;
    display: none;
}

.notification-toast.show {
    display: block;
}

.notification-toast.success {
    border-left: 4px solid #10b981;
}

.notification-toast.error {
    border-left: 4px solid #ef4444;
}

.notification-toast.warning {
    border-left: 4px solid #f59e0b;
}

.notification-toast.info {
    border-left: 4px solid #3b82f6;
}

@keyframes slideInRight {
    from {
        transform: translateX(400px);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.notification-title {
    font-weight: 700;
    font-size: 16px;
    margin-bottom: 5px;
}

.notification-message {
    font-size: 14px;
    color: #666;
}
</style>

<div id="notificationToast" class="notification-toast">
    <div class="notification-title" id="notifTitle"></div>
    <div class="notification-message" id="notifMessage"></div>
</div>

<audio id="notificationSound" preload="auto">
    <source src="../admin/vendor/sounds/arived.mp3" type="audio/mpeg">
</audio>
