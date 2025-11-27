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
    
    // Track this page as the last visited page
    if (function_exists('track_last_page')) {
        track_last_page();
    }
}
?>
 