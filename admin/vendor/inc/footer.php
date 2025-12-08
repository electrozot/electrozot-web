 <footer class="sticky-footer">
     <div class="container my-auto">
         <div class="copyright text-center my-auto">
             <span>Copyright &copy; <?php echo date('Y');?> Electrozot - Technician Booking System
             </span>
         </div>
     </div>
 </footer>

 <!-- Universal Notification System -->
 <?php include('notification-system.php'); ?>
 
 <!-- Rejection Alert Modal - Shows on all admin pages -->
 <?php 
 // Include rejection alert modal from admin directory
 $widget_path = dirname(__DIR__, 2) . '/widget-rejection-alert-modal.php';
 if(file_exists($widget_path)) {
     include($widget_path);
 } else {
     // Fallback: try relative path
     $fallback_path = '../../widget-rejection-alert-modal.php';
     if(file_exists($fallback_path)) {
         include($fallback_path);
     }
 }
 ?>