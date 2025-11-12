<?php
  session_start();
  include('vendor/inc/config.php');
  include('vendor/inc/checklogin.php');
  check_login();
  $aid=$_SESSION['a_id'];

  if(isset($_GET['del']))
  {
      $id=intval($_GET['del']);
      // Snapshot service row
      $get = $mysqli->prepare("SELECT * FROM tms_service WHERE s_id=?");
      $get->bind_param('i', $id);
      $get->execute();
      $res = $get->get_result();
      $service = $res->fetch_assoc();
      if($service){
        $payload = json_encode($service);
        $ins = $mysqli->prepare("INSERT INTO tms_recycle_bin (rb_type, rb_table, rb_object_id, rb_payload, rb_deleted_by) VALUES ('service','tms_service', ?, ?, ?)");
        $ins->bind_param('isi', $id, $payload, $aid);
        $ins->execute();
      }
      // Soft delete service to avoid cascading booking deletion
      $upd = $mysqli->prepare("UPDATE tms_service SET s_status='Inactive' WHERE s_id=?");
      $upd->bind_param('i', $id);
      $upd->execute();

      if($upd){
        $succ = "Service marked inactive and sent to Recycle Bin";
      } else {
        $err = "Try Again Later";
      }
  }
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
                 <!-- Breadcrumbs-->
                 <ol class="breadcrumb">
                     <li class="breadcrumb-item">
                         <a href="#">Services</a>
                     </li>
                     <li class="breadcrumb-item active">Manage Services</li>
                 </ol>
                 <?php if(isset($succ)) {?>
                 <script>
                 setTimeout(function() {
                         swal("Success!", "<?php echo $succ;?>!", "success");
                     },
                     100);
                 </script>

                 <?php } ?>
                 <?php if(isset($err)) {?>
                 <script>
                 setTimeout(function() {
                         swal("Failed!", "<?php echo $err;?>!", "Failed");
                     },
                     100);
                 </script>

                 <?php } ?>

                 <!-- DataTables Example -->
                 <div class="card mb-3">
                     <div class="card-header">
                         <i class="fas fa-cogs"></i>
                         Available Services
                     </div>
                     <div class="card-body">
                         <div class="table-responsive">
                             <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                 <thead>
                                     <tr>
                                         <th>#</th>
                                         <th>Service Name</th>
                                         <th>Category</th>
                                         <th>Price</th>
                                         <th>Status</th>
                                         <th>Action</th>
                                     </tr>
                                 </thead>
                                 <?php

                    $ret="SELECT * FROM tms_service "; 
                    $stmt= $mysqli->prepare($ret) ;
                    $stmt->execute();
                    $res=$stmt->get_result();
                    $cnt=1;
                    while($row=$res->fetch_object())
                {
                ?>
                                 <tbody>
                                     <tr>
                                         <td><?php echo $cnt;?></td>
                                         <td><?php echo $row->s_name;?></td>
                                         <td><?php echo $row->s_category;?></td>
                                         <td>$<?php echo number_format($row->s_price, 2);?></td>
                                         <td><?php if($row->s_status == "Active"){ echo '<span class = "badge badge-success">'.$row->s_status.'</span>'; } else { echo '<span class = "badge badge-danger">'.$row->s_status.'</span>';}?></td>
                                         <td>
                                             <a href="admin-manage-single-service.php?s_id=<?php echo $row->s_id;?>" class="badge badge-success">Update</a>
                                             <a href="admin-manage-service.php?del=<?php echo $row->s_id;?>" class="badge badge-danger">Delete</a>
                                         </td>
                                     </tr>
                                 </tbody>
                                 <?php $cnt = $cnt+1; }?>

                             </table>
                         </div>
                     </div>
                     <div class="card-footer small text-muted">Updated yesterday at 11:59 PM</div>
                 </div>
             </div>
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
     <script src="js/sb-admin.min.js"></script>

     <!-- Demo scripts for this page-->
     <script src="js/demo/datatables-demo.js"></script>
     <script src="vendor/js/swal.js"></script>
 </body>

 </html>

