 <?php
  session_start();
  include('vendor/inc/config.php');
  include('vendor/inc/checklogin.php');
  check_login();
  $aid=$_SESSION['a_id'];
?>
 <!DOCTYPE html>
 <html lang="en">

 <?php include('vendor/inc/head.php');?>

 <body id="page-top">

     <?php include("vendor/inc/nav.php");?>

     <div id="wrapper">

         <!-- Sidebar -->
         <?php include('vendor/inc/sidebar.php');?>

         <div id="content-wrapper">

             <div class="container-fluid">
                 <p>
                 </p>
                 <!-- Breadcrumbs-->
                 <ol class="breadcrumb">
                     <li class="breadcrumb-item">
                         <a href="#">Feedbacks</a>
                     </li>
                     <li class="breadcrumb-item active">Manage</li>
                 </ol>
                 
                 <?php
                 // Handle bulk publish
                 if(isset($_POST['bulk_publish'])) {
                     if(isset($_POST['selected_feedbacks']) && !empty($_POST['selected_feedbacks'])) {
                         $selected = $_POST['selected_feedbacks'];
                         $published_count = 0;
                         
                         foreach($selected as $f_id) {
                             $update_query = "UPDATE tms_feedback SET f_status = 'Published' WHERE f_id = ?";
                             $update_stmt = $mysqli->prepare($update_query);
                             $update_stmt->bind_param('i', $f_id);
                             if($update_stmt->execute()) {
                                 $published_count++;
                             }
                         }
                         
                         if($published_count > 0) {
                             echo '<div class="alert alert-success alert-dismissible fade show">
                                     <i class="fas fa-check-circle"></i> ' . $published_count . ' feedback(s) published successfully!
                                     <button type="button" class="close" data-dismiss="alert">&times;</button>
                                   </div>';
                         }
                     } else {
                         echo '<div class="alert alert-warning alert-dismissible fade show">
                                 <i class="fas fa-exclamation-triangle"></i> Please select at least one feedback to publish.
                                 <button type="button" class="close" data-dismiss="alert">&times;</button>
                               </div>';
                     }
                 }
                 ?>
                 
                 <!--Feedbacks-->
                 <div class="card mb-3">
                     <div class="card-header bg-primary text-white">
                         <i class="fas fa-comments"></i> Select Feedbacks to Publish
                     </div>
                     <div class="card-body">
                         <form method="POST" id="publishForm">
                             <div class="mb-3">
                                 <button type="button" id="selectAll" class="btn btn-sm btn-secondary">
                                     <i class="fas fa-check-square"></i> Select All
                                 </button>
                                 <button type="button" id="deselectAll" class="btn btn-sm btn-secondary">
                                     <i class="fas fa-square"></i> Deselect All
                                 </button>
                                 <button type="submit" name="bulk_publish" class="btn btn-sm btn-success float-right">
                                     <i class="fas fa-check-circle"></i> Publish Selected
                                 </button>
                             </div>
                             
                             <div class="table-responsive">
                                 <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                                     <thead class="thead-light">
                                         <tr>
                                             <th width="50px">
                                                 <input type="checkbox" id="selectAllCheckbox">
                                             </th>
                                             <th width="50px">#</th>
                                             <th>Name</th>
                                             <th>Feedback</th>
                                             <th width="100px">Status</th>
                                         </tr>
                                     </thead>

                                     <tbody>
                                         <?php
                                         $ret="SELECT * FROM tms_feedback ORDER BY f_id DESC"; 
                                         $stmt= $mysqli->prepare($ret) ;
                                         $stmt->execute();
                                         $res=$stmt->get_result();
                                         $cnt=1;
                                         while($row=$res->fetch_object())
                                         {
                                         ?>
                                         
                                         <tr>
                                             <td class="text-center">
                                                 <input type="checkbox" name="selected_feedbacks[]" value="<?php echo $row->f_id;?>" class="feedback-checkbox">
                                             </td>
                                             <td><?php echo $cnt;?></td>
                                             <td><?php echo $row->f_uname;?></td>
                                             <td><?php echo $row->f_content;?></td>
                                             <td class="text-center">
                                                 <?php if($row->f_status == 'Published'): ?>
                                                     <span class="badge badge-success">Published</span>
                                                 <?php else: ?>
                                                     <span class="badge badge-warning">Pending</span>
                                                 <?php endif; ?>
                                             </td>
                                         </tr>
                                         <?php  $cnt = $cnt +1; }?>
                                     </tbody>
                                 </table>
                             </div>
                         </form>
                     </div>
                     <div class="card-footer small text-muted">
                         <?php
                         date_default_timezone_set("Africa/Nairobi");
                         echo "Generated On : " . date("h:i:sa");
                         ?>
                     </div>
                 </div>
                 
                 <script>
                 // Select/Deselect All functionality
                 document.getElementById('selectAllCheckbox').addEventListener('change', function() {
                     var checkboxes = document.getElementsByClassName('feedback-checkbox');
                     for(var i = 0; i < checkboxes.length; i++) {
                         checkboxes[i].checked = this.checked;
                     }
                 });
                 
                 document.getElementById('selectAll').addEventListener('click', function() {
                     var checkboxes = document.getElementsByClassName('feedback-checkbox');
                     for(var i = 0; i < checkboxes.length; i++) {
                         checkboxes[i].checked = true;
                     }
                     document.getElementById('selectAllCheckbox').checked = true;
                 });
                 
                 document.getElementById('deselectAll').addEventListener('click', function() {
                     var checkboxes = document.getElementsByClassName('feedback-checkbox');
                     for(var i = 0; i < checkboxes.length; i++) {
                         checkboxes[i].checked = false;
                     }
                     document.getElementById('selectAllCheckbox').checked = false;
                 });
                 
                 // Confirm before publishing
                 document.getElementById('publishForm').addEventListener('submit', function(e) {
                     var checkedBoxes = document.querySelectorAll('.feedback-checkbox:checked');
                     if(checkedBoxes.length === 0) {
                         e.preventDefault();
                         alert('Please select at least one feedback to publish.');
                         return false;
                     }
                     
                     if(!confirm('Are you sure you want to publish ' + checkedBoxes.length + ' feedback(s)?')) {
                         e.preventDefault();
                         return false;
                     }
                 });
                 </script>
                 <!-- /.container-fluid -->
                 
                 <!-- Sticky Footer -->
                 <?php include("vendor/inc/footer.php");?>
             </div>
             <!-- /.content-wrapper -->

         </div>
         <!-- /#wrapper -->

         <!-- Scroll to Top Button-->
         <a class="scroll-to-top rounded" href="#page-top">
             <i class="fas fa-angle-up"></i>
         </a>
         
         <!-- Logout Modal-->
         <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
             <div class="modal-dialog" role="document">
                 <div class="modal-content">
                     <div class="modal-header">
                         <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                         <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                             <span aria-hidden="true">×</span>
                         </button>
                     </div>
                     <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                     <div class="modal-footer">
                         <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                         <a class="btn btn-danger" href="admin-logout.php">Logout</a>
                     </div>
                 </div>
             </div>
         </div>
         
         <!-- Bootstrap core JavaScript-->
         <script src="vendor/jquery/jquery.min.js"></script>
         <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

         <!-- Core plugin JavaScript-->
         <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

         <!-- Page level plugin JavaScript-->
         <script src="vendor/datatables/jquery.dataTables.js"></script>
         <script src="vendor/datatables/dataTables.bootstrap4.js"></script>

         <!-- Custom scripts for all pages-->
         <script src="vendor/js/sb-admin.min.js"></script>

         <!-- Demo scripts for this page-->
         <script src="vendor/js/demo/datatables-demo.js"></script>

 </body>

 </html>