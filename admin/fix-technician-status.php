<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

$action = isset($_GET['action']) ? $_GET['action'] : '';
$message = '';
$error = '';

// Fix "Free" status
