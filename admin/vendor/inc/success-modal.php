<!-- Success Modal with Auto-Redirect -->
<style>
.success-modal-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    z-index: 9999;
    justify-content: center;
    align-items: center;
    animation: fadeIn 0.3s ease;
}

.success-modal-overlay.show {
    display: flex;
}

.success-modal-box {
    background: white;
    border-radius: 20px;
    padding: 40px;
    text-align: center;
    max-width: 500px;
    width: 90%;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    animation: slideUp 0.4s ease;
    position: relative;
}

.success-checkmark {
    width: 100px;
    height: 100px;
    margin: 0 auto 20px;
    border-radius: 50%;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    animation: scaleIn 0.5s ease;
}

.success-checkmark i {
    font-size: 50px;
    color: white;
    animation: checkPop 0.6s ease 0.3s both;
}

.success-modal-title {
    font-size: 28px;
    font-weight: 900;
    color: #10b981;
    margin-bottom: 15px;
}

.success-modal-message {
    font-size: 16px;
    color: #64748b;
    margin-bottom: 25px;
    line-height: 1.6;
}

.success-modal-redirect {
    font-size: 14px;
    color: #94a3b8;
    font-weight: 600;
}

.success-modal-redirect .countdown {
    color: #10b981;
    font-weight: 900;
    font-size: 18px;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideUp {
    from {
        transform: translateY(50px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

@keyframes scaleIn {
    from {
        transform: scale(0);
    }
    to {
        transform: scale(1);
    }
}

@keyframes checkPop {
    0% {
        transform: scale(0);
        opacity: 0;
    }
    50% {
        transform: scale(1.2);
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}
</style>

<div id="successModal" class="success-modal-overlay">
    <div class="success-modal-box">
        <div class="success-checkmark">
            <i class="fas fa-check"></i>
        </div>
        <h2 class="success-modal-title">Success!</h2>
        <p class="success-modal-message" id="successMessage">Operation completed successfully</p>
        <p class="success-modal-redirect">
            Redirecting in <span class="countdown" id="countdown">3</span> seconds...
        </p>
    </div>
</div>

<script>
function showSuccessModal(message, redirectUrl, delay = 3000) {
    const modal = document.getElementById('successModal');
    const messageEl = document.getElementById('successMessage');
    const countdownEl = document.getElementById('countdown');
    
    // Set message
    messageEl.textContent = message;
    
    // Show modal
    modal.classList.add('show');
    
    // Countdown
    let seconds = Math.ceil(delay / 1000);
    countdownEl.textContent = seconds;
    
    const countdownInterval = setInterval(() => {
        seconds--;
        countdownEl.textContent = seconds;
        if (seconds <= 0) {
            clearInterval(countdownInterval);
        }
    }, 1000);
    
    // Redirect after delay
    setTimeout(() => {
        window.location.href = redirectUrl;
    }, delay);
}

// Auto-trigger if success parameter is in URL
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const success = urlParams.get('success');
    const message = urlParams.get('message');
    const redirect = urlParams.get('redirect');
    const quick = urlParams.get('quick'); // Quick mode for custom bookings
    
    if (success === '1' && message && redirect) {
        // Use 2 seconds for quick mode (custom bookings), 3 seconds for regular
        const delay = quick === '1' ? 2000 : 3000;
        showSuccessModal(decodeURIComponent(message), decodeURIComponent(redirect), delay);
    }
});
</script>
