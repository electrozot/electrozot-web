<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

$period = isset($_GET['period']) ? $_GET['period'] : 'today';

// Calculate date ranges
switch($period)