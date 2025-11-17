<?php
// Check if technician is logged in
if(!isset($_SESSION['t_id']) || !isset($_SESSION['t_name'])){
    header('Location: index.php');
    exit();
}
?>
