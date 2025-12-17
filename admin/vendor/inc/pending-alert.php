<?php
// Check pending bookings count for alert
$pending_count_query = "SELECT COUNT(*) as pending_count FROM tms_service_booking WHERE sb_status = 'Pending'";
$pending_result = $mysqli->query($pending_count_query);
$pending_count = 0;
if($pending_result) {
    $pending_count = $pending_result->fetch_object()->pending_count;
}

// Check if alert was already shown for this threshold
$show_alert_12 = false;
$show_alert_25 = false;

if($pending_count >= 12 && !isset($_SESSION['alert_shown_12'])) {
    $show_alert_12 = true;
}

if($pending_count >= 25 && !isset($_SESSION['alert_shown_25'])) {
    $show_alert_25 = true;
}

// Dismiss alert
if(isset($_POST['dismiss_alert'])) {
    $alert_type = $_POST['alert_type'];
    if($alert_type == '12') {
        $_SESSION['alert_shown_12'] = true;
    } elseif($alert_type == '25') {
        $_SESSION['alert_shown_25'] = true;
    }
    header("Location: admin-notifications.php");
    exit();
}

// Reset alerts when pending count drops below 5
if($pending_count < 5) {
    unset($_SESSION['alert_shown_12']);
    unset($_SESSION['alert_shown_25']);
}
?>

<!-- Pending Bookings Alert Modal -->
<?php if($show_alert_12 || $show_alert_25): ?>
<div class="modal fade show" id="pendingAlertModal" tabindex="-1" role="dialog" style="display: block; background: rgba(0,0,0,0.8); z-index: 99999;" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document" style="max-width: 450px;">
        <div class="modal-content" style="border: 3px solid #dc3545; border-radius: 12px; box-shadow: 0 8px 30px rgba(220, 53, 69, 0.5);">
            <div class="modal-header" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); border: none; padding: 15px;">
                <h5 class="modal-title text-white" style="font-weight: 800; font-size: 18px;">
                    <i class="fas fa-exclamation-triangle" style="animation: shake 0.5s infinite;"></i>
                    URGENT ALERT!
                </h5>
            </div>
            <div class="modal-body text-center" style="padding: 20px 15px;">
                <div style="font-size: 50px; color: #dc3545; margin-bottom: 10px;">
                    <i class="fas fa-bell" style="animation: bellRing 1s infinite;"></i>
                </div>
                <h4 style="color: #dc3545; font-weight: 800; margin-bottom: 10px; font-size: 20px;">
                    <?php if($show_alert_25): ?>
                        BREAK TIME IS OVER! 🚨
                    <?php else: ?>
                        REST IS OVER! ⏰
                    <?php endif; ?>
                </h4>
                <p style="font-size: 14px; font-weight: 600; color: #333; margin-bottom: 10px;">
                    Back to work! You have pending bookings to assign.
                </p>
                <div style="background: #fff3cd; padding: 12px; border-radius: 8px; border-left: 4px solid #ffc107; margin: 10px 0;">
                    <h3 style="color: #856404; font-weight: 900; margin: 0; font-size: 22px;">
                        <i class="fas fa-clipboard-list"></i> <?php echo $pending_count; ?> PENDING
                    </h3>
                </div>
                <p style="font-size: 12px; color: #666; margin-bottom: 10px;">
                    <?php if($show_alert_25): ?>
                        <strong style="color: #dc3545;">Critical Level!</strong> Assign technicians now.
                    <?php else: ?>
                        Time to clear the queue!
                    <?php endif; ?>
                </p>
            </div>
            <div class="modal-footer" style="border: none; justify-content: center; padding: 15px;">
                <form method="POST" style="width: 100%;">
                    <input type="hidden" name="alert_type" value="<?php echo $show_alert_25 ? '25' : '12'; ?>">
                    <button type="submit" name="dismiss_alert" class="btn btn-danger btn-lg btn-block" style="font-size: 16px; font-weight: 800; padding: 12px; border-radius: 8px;">
                        <i class="fas fa-check-circle"></i> OK, LET'S DO THIS!
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes shake {
        0%, 100% { transform: rotate(0deg); }
        25% { transform: rotate(-10deg); }
        75% { transform: rotate(10deg); }
    }
    
    @keyframes bellRing {
        0%, 100% { transform: rotate(0deg); }
        10%, 30% { transform: rotate(-15deg); }
        20%, 40% { transform: rotate(15deg); }
        50% { transform: rotate(0deg); }
    }
</style>

<script>
(function() {
    // Play alert sound every 3 seconds - using correct path
    const alertSound = new Audio('vendor/sounds/arived.mp3');
    alertSound.volume = 1.0; // Maximum volume
    alertSound.preload = 'auto';
    
    let soundInterval;
    let audioEnabled = false;
    
    function playAlertSound() {
        alertSound.currentTime = 0;
        alertSound.play()
            .then(() => {
                audioEnabled = true;
                console.log('🔊 ALERT SOUND PLAYING!');
            })
            .catch(e => {
                if(!audioEnabled) {
                    console.log('⚠️ Audio blocked - click anywhere to enable');
                }
            });
    }
    
    // Enable audio on first click
    function enableAudio() {
        alertSound.play().then(() => {
            alertSound.pause();
            alertSound.currentTime = 0;
            audioEnabled = true;
            console.log('✅ Audio enabled - alert will play');
            // Play immediately after enabling
            playAlertSound();
        }).catch(() => {});
    }
    
    document.addEventListener('click', enableAudio, { once: true });
    document.addEventListener('keydown', enableAudio, { once: true });
    
    // Try to play immediately
    setTimeout(playAlertSound, 500);
    
    // Repeat every 3 seconds
    soundInterval = setInterval(playAlertSound, 3000);
    
    // Stop sound when form is submitted
    const alertForm = document.querySelector('#pendingAlertModal form');
    if(alertForm) {
        alertForm.addEventListener('submit', function() {
            clearInterval(soundInterval);
            alertSound.pause();
            alertSound.currentTime = 0;
            console.log('🔇 Alert sound stopped');
        });
    }
    
    console.log('🎵 Alert system initialized - sound file: vendor/sounds/arived.mp3');
})();
</script>
<?php endif; ?>
