 <?php
function check_login()
{
    if(strlen($_SESSION['a_id'])==0)
    {
        $host = $_SERVER['HTTP_HOST'];
        $uri  = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
        $extra="index.php";
        $_SESSION["a_id"]="";
        header("Location: http://$host$uri/$extra");
        exit;
    }
    
    // Refresh session cookie on each page load to extend timeout to 15 hours
    if (session_status() === PHP_SESSION_ACTIVE) {
        setcookie(session_name(), session_id(), time() + 54000, '/', '', false, true);
    }
    
    // Track this page as the last visited page
    if (function_exists('track_last_page')) {
        track_last_page();
    }
}
?>
 