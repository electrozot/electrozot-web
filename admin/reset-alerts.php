<?php
session_start();
unset($_SESSION['alert_shown_12']);
unset($_SESSION['alert_shown_25']);
echo "Alerts reset! <a href='admin-dashboard.php'>Go to Dashboard</a>";
?>
