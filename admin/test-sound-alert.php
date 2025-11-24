<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Sound Alert</title>
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="vendor/fontawesome-free/css/all.min.css">
    <style>
        body { background: #f5f7fa; padding: 30px; }
        .test-card { background: white; border-radius: 10px; padding: 30px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .test-btn { padding: 20px 40px; font-size: 18px; margin: 10px; }
        .status-box { background: #f9fafb; padding: 20px; border-radius: 5px; margin: 15px 0; border-left: 4px solid #667eea; }
        .success { border-left-color: #10b981; background: #ecfdf5; }
        .error { border-left-color: #ef4444; background: #fef2f2; }
        .info { border-left-color: #3b82f6; background: #dbeafe; }
    </style>
</head>
<body>
    <div class="container">
        <div class="test-card">
            <h2><i class="fas fa-volume-up"></i> Sound Alert Test</h2>
            <p class="text-muted">Test if notification sound is working correctly</p>
        </div>
        
        <div class="test-card">
            <h4><i class="fas fa-info-circle"></i> Sound File Status</h4>
            <?php
            $sound_file = 'vendor/sounds/arived.mp3';
            if (file_exists($sound_file)) {
                $file_size = filesize($sound_file);
                echo '<div class="status-box success">';
                echo '<i class="fas fa-check-circle"></i> <strong>Sound file found!</strong><br>';
                echo 'Location: <code>' . $sound_file . '</code><br>';
                echo 'Size: ' . round($file_size / 1024, 2) . ' KB';
                echo '</div>';
            } else {
                echo '<div class="status-box error">';
                echo '<i class="fas fa-times-circle"></i> <strong>Sound file NOT found!</strong><br>';
                echo 'Expected location: <code>' . $sound_file . '</code><br>';
                echo 'Please add the arived.mp3 file to this location.';
                echo '</div>';
            }
            ?>
        </div>
        
        <div class="test-card">
            <h4><i class="fas fa-flask"></i> Sound Tests</h4>
            
            <div class="status-box info">
                <i class="fas fa-lightbulb"></i> <strong>Important:</strong> 
                Chrome requires user interaction before playing sound. 
                Click any button below to enable and test sound.
            </div>
            
            <button onclick="testSound1()" class="btn btn-primary btn-lg test-btn">
                <i class="fas fa-play"></i> Test Sound (Method 1)
            </button>
            
            <button onclick="testSound2()" class="btn btn-success btn-lg test-btn">
                <i class="fas fa-volume-up"></i> Test Sound (Method 2)
            </button>
            
            <button onclick="testSound3()" class="btn btn-info btn-lg test-btn">
                <i class="fas fa-bell"></i> Test Sound (Method 3)
            </button>
            
            <div id="testResult" style="margin-top: 20px;"></div>
        </div>
        
        <div class="test-card">
            <h4><i class="fas fa-code"></i> Console Output</h4>
            <p>Open browser console (F12) to see detailed logs</p>
            <div id="consoleOutput" class="status-box" style="font-family: monospace; font-size: 12px; max-height: 300px; overflow-y: auto;">
                Waiting for test...
            </div>
        </div>
        
        <div class="test-card">
            <h4><i class="fas fa-check-circle"></i> Verification Checklist</h4>
            <ul class="list-group">
                <li class="list-group-item">
                    <input type="checkbox" id="check1"> Sound file exists at correct location
                </li>
                <li class="list-group-item">
                    <input type="checkbox" id="check2"> Clicked on page to enable sound
                </li>
                <li class="list-group-item">
                    <input type="checkbox" id="check3"> Heard sound when clicking test button
                </li>
                <li class="list-group-item">
                    <input type="checkbox" id="check4"> No errors in browser console
                </li>
                <li class="list-group-item">
                    <input type="checkbox" id="check5"> Browser volume is not muted
                </li>
            </ul>
        </div>
        
        <div class="test-card">
            <h4><i class="fas fa-link"></i> Quick Links</h4>
            <a href="test-unified-notifications.php" class="btn btn-primary">
                <i class="fas fa-bell"></i> Test Full Notification System
            </a>
            <a href="admin-dashboard.php" class="btn btn-success">
                <i class="fas fa-home"></i> Dashboard
            </a>
            <a href="cleanup-old-notifications.php" class="btn btn-warning">
                <i class="fas fa-broom"></i> Cleanup Old Systems
            </a>
        </div>
    </div>
    
    <!-- Audio Element -->
    <audio id="testSound" preload="auto">
        <source src="vendor/sounds/arived.mp3" type="audio/mpeg">
    </audio>
    
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    
    <script>
    const consoleOutput = document.getElementById('consoleOutput');
    const testResult = document.getElementById('testResult');
    
    function log(message, type = 'info') {
        const timestamp = new Date().toLocaleTimeString();
        const color = type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#667eea';
        consoleOutput.innerHTML += `<div style="color: ${color};">[${timestamp}] ${message}</div>`;
        consoleOutput.scrollTop = consoleOutput.scrollHeight;
        console.log(message);
    }
    
    function showResult(message, success) {
        testResult.innerHTML = `
            <div class="alert alert-${success ? 'success' : 'danger'}">
                <i class="fas fa-${success ? 'check' : 'times'}-circle"></i> ${message}
            </div>
        `;
    }
    
    // Method 1: Direct play
    function testSound1() {
        log('Testing sound (Method 1: Direct play)...', 'info');
        const sound = document.getElementById('testSound');
        
        if (!sound) {
            log('❌ Sound element not found!', 'error');
            showResult('Sound element not found!', false);
            return;
        }
        
        sound.currentTime = 0;
        sound.volume = 0.8;
        
        sound.play()
            .then(() => {
                log('✅ Sound played successfully!', 'success');
                showResult('✅ Sound played successfully! You should hear it now.', true);
                document.getElementById('check3').checked = true;
            })
            .catch(error => {
                log('❌ Sound play failed: ' + error.message, 'error');
                showResult('❌ Sound blocked by browser. Error: ' + error.message, false);
            });
    }
    
    // Method 2: With load
    function testSound2() {
        log('Testing sound (Method 2: With load)...', 'info');
        const sound = document.getElementById('testSound');
        
        if (!sound) {
            log('❌ Sound element not found!', 'error');
            showResult('Sound element not found!', false);
            return;
        }
        
        sound.load();
        sound.volume = 0.8;
        
        setTimeout(() => {
            sound.play()
                .then(() => {
                    log('✅ Sound played successfully!', 'success');
                    showResult('✅ Sound played successfully! You should hear it now.', true);
                    document.getElementById('check3').checked = true;
                })
                .catch(error => {
                    log('❌ Sound play failed: ' + error.message, 'error');
                    showResult('❌ Sound blocked by browser. Error: ' + error.message, false);
                });
        }, 100);
    }
    
    // Method 3: Multiple attempts
    function testSound3() {
        log('Testing sound (Method 3: Multiple attempts)...', 'info');
        const sound = document.getElementById('testSound');
        
        if (!sound) {
            log('❌ Sound element not found!', 'error');
            showResult('Sound element not found!', false);
            return;
        }
        
        // Attempt 1
        sound.load();
        sound.volume = 0.8;
        sound.currentTime = 0;
        
        const playPromise = sound.play();
        
        if (playPromise !== undefined) {
            playPromise
                .then(() => {
                    log('✅ Sound played on first attempt!', 'success');
                    showResult('✅ Sound played successfully! You should hear it now.', true);
                    document.getElementById('check3').checked = true;
                })
                .catch(error => {
                    log('⚠️ First attempt failed, trying again...', 'info');
                    
                    // Attempt 2
                    setTimeout(() => {
                        sound.play()
                            .then(() => {
                                log('✅ Sound played on second attempt!', 'success');
                                showResult('✅ Sound played successfully! You should hear it now.', true);
                                document.getElementById('check3').checked = true;
                            })
                            .catch(error2 => {
                                log('❌ All attempts failed: ' + error2.message, 'error');
                                showResult('❌ Sound blocked. Please check browser settings.', false);
                            });
                    }, 500);
                });
        }
    }
    
    // Auto-check file existence
    <?php if (file_exists($sound_file)): ?>
    document.getElementById('check1').checked = true;
    log('✅ Sound file exists', 'success');
    <?php else: ?>
    log('❌ Sound file not found!', 'error');
    <?php endif; ?>
    
    // Enable sound on page interaction
    document.addEventListener('click', function() {
        document.getElementById('check2').checked = true;
        log('✅ User interaction detected - sound enabled', 'success');
    }, { once: true });
    
    // Check browser volume
    window.addEventListener('load', function() {
        log('Page loaded. Click any test button to play sound.', 'info');
        
        // Check if audio is supported
        const audio = document.createElement('audio');
        if (audio.canPlayType('audio/mpeg')) {
            log('✅ Browser supports MP3 audio', 'success');
        } else {
            log('⚠️ Browser may not support MP3 audio', 'error');
        }
    });
    </script>
</body>
</html>
