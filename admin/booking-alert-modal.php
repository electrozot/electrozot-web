<?php if($show_alert && $alert_level > 0): ?>
<!-- Booking Alert Modal - Fresh System -->
<div class="booking-alert-overlay" style="
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.85);
    z-index: 99999;
    display: flex;
    align-items: center;
    justify-content: center;
">
    <div class="booking-alert-modal" style="
        background: white;
        border-radius: 15px;
        max-width: 500px;
        width: 90%;
        box-shadow: 0 10px 50px rgba(220, 53, 69, 0.5);
        border: 4px solid <?php echo $alert_level >= 25 ? '#dc3545' : '#ffc107'; ?>;
        animation: alertPulse 0.5s ease-in-out;
    ">
        <!-- Header -->
        <div style="
            background: linear-gradient(135deg, <?php echo $alert_level >= 25 ? '#dc3545 0%, #c82333 100%' : '#ffc107 0%, #ff9800 100%'; ?>);
            padding: 20px;
            border-radius: 11px 11px 0 0;
            text-align: center;
        ">
            <div style="font-size: 60px; margin-bottom: 10px;">
                <i class="fas fa-bell" style="color: white; animation: bellShake 1s infinite;"></i>
            </div>
            <h3 style="color: white; margin: 0; font-weight: 800; font-size: 24px;">
                <?php if($alert_level >= 25): ?>
                    🚨 CRITICAL ALERT!
                <?php else: ?>
                    ⚠️ ATTENTION NEEDED!
                <?php endif; ?>
            </h3>
        </div>
        
        <!-- Body -->
        <div style="padding: 30px; text-align: center;">
            <h4 style="color: #333; font-weight: 700; margin-bottom: 15px;">
                <?php if($alert_level >= 25): ?>
                    Break Time is Over! 🚨
                <?php else: ?>
                    Time to Get Back to Work! ⏰
                <?php endif; ?>
            </h4>
            
            <p style="color: #666; font-size: 15px; margin-bottom: 20px;">
                You have bookings that need immediate action
            </p>
            
            <!-- Count Display -->
            <div style="
                background: <?php echo $alert_level >= 25 ? '#ffe6e6' : '#fff8e1'; ?>;
                border-left: 5px solid <?php echo $alert_level >= 25 ? '#dc3545' : '#ffc107'; ?>;
                padding: 20px;
                border-radius: 8px;
                margin-bottom: 20px;
            ">
                <div style="font-size: 48px; font-weight: 900; color: <?php echo $alert_level >= 25 ? '#dc3545' : '#ff9800'; ?>; margin-bottom: 5px;">
                    <?php echo $bookings_needing_action; ?>
                </div>
                <div style="font-size: 14px; font-weight: 600; color: #666; text-transform: uppercase; letter-spacing: 1px;">
                    Bookings Need Action
                </div>
                
                <?php if($breakdown): ?>
                <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid rgba(0,0,0,0.1);">
                    <div style="display: flex; justify-content: space-around; flex-wrap: wrap; gap: 10px;">
                        <?php if($breakdown->pending > 0): ?>
                        <div style="text-align: center;">
                            <div style="font-size: 20px; font-weight: 700; color: #ffc107;"><?php echo $breakdown->pending; ?></div>
                            <div style="font-size: 11px; color: #666;">Pending</div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if($breakdown->rejected > 0): ?>
                        <div style="text-align: center;">
                            <div style="font-size: 20px; font-weight: 700; color: #dc3545;"><?php echo $breakdown->rejected; ?></div>
                            <div style="font-size: 11px; color: #666;">Rejected</div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if($breakdown->not_done > 0): ?>
                        <div style="text-align: center;">
                            <div style="font-size: 20px; font-weight: 700; color: #ff5722;"><?php echo $breakdown->not_done; ?></div>
                            <div style="font-size: 11px; color: #666;">Not Done</div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if($breakdown->unassigned > 0): ?>
                        <div style="text-align: center;">
                            <div style="font-size: 20px; font-weight: 700; color: #2196F3;"><?php echo $breakdown->unassigned; ?></div>
                            <div style="font-size: 11px; color: #666;">Unassigned</div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            <p style="color: #999; font-size: 13px; margin-bottom: 20px;">
                <?php if($alert_level >= 25): ?>
                    <strong style="color: #dc3545;">Critical level reached!</strong> Assign technicians immediately.
                <?php else: ?>
                    Please assign technicians and clear the queue.
                <?php endif; ?>
            </p>
            
            <!-- Dismiss Button -->
            <form method="POST" id="dismissAlertForm">
                <input type="hidden" name="alert_level" value="<?php echo $alert_level; ?>">
                <button type="submit" name="dismiss_booking_alert" style="
                    background: linear-gradient(135deg, <?php echo $alert_level >= 25 ? '#dc3545 0%, #c82333 100%' : '#ffc107 0%, #ff9800 100%'; ?>);
                    color: white;
                    border: none;
                    padding: 15px 40px;
                    border-radius: 8px;
                    font-size: 16px;
                    font-weight: 700;
                    cursor: pointer;
                    width: 100%;
                    transition: transform 0.2s;
                " onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                    <i class="fas fa-check-circle"></i> OK, LET'S DO THIS!
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Alert Styles -->
<style>
    @keyframes alertPulse {
        0% { transform: scale(0.8); opacity: 0; }
        100% { transform: scale(1); opacity: 1; }
    }
    
    @keyframes bellShake {
        0%, 100% { transform: rotate(0deg); }
        10%, 30% { transform: rotate(-15deg); }
        20%, 40% { transform: rotate(15deg); }
        50% { transform: rotate(0deg); }
    }
</style>

<!-- Alert Sound Script -->
<script>
(function() {
    'use strict';
    
    // Sound configuration
    const SOUND_FILE = 'vendor/sounds/arived.mp3';
    const SOUND_INTERVAL = 3000; // Play every 3 seconds
    const SOUND_VOLUME = 0.7;
    
    let soundIntervalId = null;
    let audioElement = null;
    
    // Initialize audio
    function initAudio() {
        audioElement = new Audio(SOUND_FILE);
        audioElement.volume = SOUND_VOLUME;
        
        // Preload
        audioElement.load();
        
        console.log('🔊 Alert sound system initialized');
    }
    
    // Play sound
    function playAlertSound() {
        if (!audioElement) {
            initAudio();
        }
        
        audioElement.currentTime = 0;
        audioElement.play()
            .then(() => {
                console.log('🔊 Alert sound played');
            })
            .catch(error => {
                console.warn('⚠️ Sound play failed:', error.message);
                // Try to enable on user interaction
                enableAudioOnInteraction();
            });
    }
    
    // Enable audio on user interaction (browser requirement)
    function enableAudioOnInteraction() {
        const enableHandler = function() {
            if (audioElement) {
                audioElement.play().then(() => {
                    console.log('✅ Audio enabled by user interaction');
                }).catch(() => {});
            }
            document.removeEventListener('click', enableHandler);
            document.removeEventListener('keydown', enableHandler);
        };
        
        document.addEventListener('click', enableHandler, { once: true });
        document.addEventListener('keydown', enableHandler, { once: true });
    }
    
    // Start alert sound loop
    function startAlertSound() {
        // Play immediately
        setTimeout(playAlertSound, 500);
        
        // Then repeat every 3 seconds
        soundIntervalId = setInterval(playAlertSound, SOUND_INTERVAL);
        
        console.log('🔔 Alert sound loop started');
    }
    
    // Stop alert sound
    function stopAlertSound() {
        if (soundIntervalId) {
            clearInterval(soundIntervalId);
            soundIntervalId = null;
        }
        
        if (audioElement) {
            audioElement.pause();
            audioElement.currentTime = 0;
        }
        
        console.log('🔇 Alert sound stopped');
    }
    
    // Initialize on page load
    initAudio();
    startAlertSound();
    
    // Stop sound when form is submitted
    const dismissForm = document.getElementById('dismissAlertForm');
    if (dismissForm) {
        dismissForm.addEventListener('submit', function() {
            stopAlertSound();
        });
    }
    
    // Enable audio on first interaction
    enableAudioOnInteraction();
    
})();
</script>
<?php endif; ?>
