 <!-- Author By: MH RONY
Author Website: https://developerrony.com
Github Link: https://github.com/dev-mhrony
Youtube Link: https://www.youtube.com/channel/UChYhUxkwDNialcxj-OFRcDw
--><?php
  session_start();
  include('vendor/inc/config.php');
  include('vendor/inc/checklogin.php');
  check_login();
  $aid=$_SESSION['a_id'];
  //Update Technician
  if(isset($_POST['update_tech']))
    {
            $t_id = $_GET['t_id'];
            $t_name=$_POST['t_name'];
            $t_id_no = $_POST['t_id_no'];
            $t_category=$_POST['t_category'];
            $t_status=$_POST['t_status'];
            $t_specialization=$_POST['t_specialization'];
            $t_experience=$_POST['t_experience'];
            $t_pic=$_FILES["t_pic"]["name"];
            move_uploaded_file($_FILES["t_pic"]["tmp_name"],"../vendor/img/".$_FILES["t_pic"]["name"]);
            $query="update tms_technician set t_name=?, t_id_no=?, t_specialization=?, t_category=?, t_experience=?, t_pic=?, t_status=? where t_id = ?";
            $stmt = $mysqli->prepare($query);
            $rc=$stmt->bind_param('sssssssi', $t_name, $t_id_no, $t_specialization, $t_category, $t_experience, $t_pic, $t_status, $t_id);
            $stmt->execute();
                if($stmt)
                {
                    $succ = "Technician Updated";
                }
                else 
                {
                    $err = "Please Try Again Later";
                }
            }
?>
 <!-- Author By: MH RONY
Author Website: https://developerrony.com
Github Link: https://github.com/dev-mhrony
Youtube Link: https://www.youtube.com/channel/UChYhUxkwDNialcxj-OFRcDw
-->
 <!DOCTYPE html>
 <html lang="en">

 <?php include('vendor/inc/head.php');?>

 <body id="page-top">
     <!--Start Navigation Bar-->
     <?php include("vendor/inc/nav.php");?>
     <!--Navigation Bar-->
     <!-- Author By: MH RONY
Author Website: https://developerrony.com
Github Link: https://github.com/dev-mhrony
Youtube Link: https://www.youtube.com/channel/UChYhUxkwDNialcxj-OFRcDw
-->
     <div id="wrapper">

         <!-- Sidebar -->
         <?php include("vendor/inc/sidebar.php");?>
         <!--End Sidebar-->
         <div id="content-wrapper">

             <div class="container-fluid">
                 <?php if(isset($succ)) {?>
                 <!--This code for injecting an alert-->
                 <script>
                 setTimeout(function() {
                         swal("Success!", "<?php echo $succ;?>!", "success");
                     },
                     100);
                 </script>

                 <?php } ?>
                 <?php if(isset($err)) {?>
                 <!--This code for injecting an alert-->
                 <script>
                 setTimeout(function() {
                         swal("Failed!", "<?php echo $err;?>!", "Failed");
                     },
                     100);
                 </script>

                 <?php } ?>
                 <!-- Author By: MH RONY
Author Website: https://developerrony.com
Github Link: https://github.com/dev-mhrony
Youtube Link: https://www.youtube.com/channel/UChYhUxkwDNialcxj-OFRcDw
-->
                 <p>
                     <marquee onMouseOver="this.stop()" onMouseOut="this.start()">This code is not for sale. Its sole owner is Code Camp BD For any need you can message me <a href="https://www.facebook.com/dev.mhrony">MH RONY</a> and don't forget to <a href="https://www.youtube.com/@codecampbdofficial">subscribe</a> the youtube channel.</marquee>
                 </p>
                 <!-- Breadcrumbs-->
                 <ol class="breadcrumb">
                     <li class="breadcrumb-item">
                         <a href="#">Technicians</a>
                     </li>
                     <li class="breadcrumb-item active">Update Technician</li>
                 </ol>
                 <hr>
                 <div class="card">
                     <div class="card-header">
                         Update Technician
                     </div>
                     <div class="card-body">
                         <!--Update Technician Form-->
                         <?php
            $aid=$_GET['t_id'];
            $ret="select * from tms_technician where t_id=?";
            $stmt= $mysqli->prepare($ret) ;
            $stmt->bind_param('i',$aid);
            $stmt->execute() ;//ok
            $res=$stmt->get_result();
            //$cnt=1;
            while($row=$res->fetch_object())
        {
        ?>
                         <!-- Author By: MH RONY
        Author Website: https://developerrony.com
        Github Link: https://github.com/dev-mhrony
        Youtube Link: https://www.youtube.com/channel/UChYhUxkwDNialcxj-OFRcDw
        -->
                         <form method="POST" enctype="multipart/form-data">
                             <div class="form-group">
                                 <label for="exampleInputEmail1">Technician Name</label>
                                 <input type="text" value="<?php echo $row->t_name;?>" required class="form-control" id="exampleInputEmail1" name="t_name">
                             </div>
                             <div class="form-group">
                                 <label for="exampleInputEmail1">Technician ID Number</label>
                                 <input type="text" value="<?php echo $row->t_id_no;?>" class="form-control" id="exampleInputEmail1" name="t_id_no">
                             </div>

                             <div class="form-group">
                                 <label for="exampleInputEmail1">Specialization</label>
                                 <input type="text" value="<?php echo $row->t_specialization;?>" class="form-control" id="exampleInputEmail1" name="t_specialization">
                             </div>

                             <div class="form-group">
                                 <label for="exampleInputEmail1">Years of Experience</label>
                                 <input type="text" value="<?php echo $row->t_experience;?>" class="form-control" id="exampleInputEmail1" name="t_experience">
                             </div>

                             <div class="form-group">
                                 <label for="exampleFormControlSelect1">Service Category</label>
                                 <select class="form-control" name="t_category" id="exampleFormControlSelect1">
                                     <option>Electrical</option>
                                     <option>Plumbing</option>
                                     <option>HVAC</option>
                                     <option>Appliance</option>
                                     <option>General</option>

                                 </select>
                             </div>

                             <div class="form-group">
                                 <label for="exampleFormControlSelect1">Technician Status</label>
                                 <select class="form-control" name="t_status" id="exampleFormControlSelect1">
                                     <option>Booked</option>
                                     <option>Available</option>
                                 </select>
                             </div>
                             <!-- Author By: MH RONY
Author Website: https://developerrony.com
Github Link: https://github.com/dev-mhrony
Youtube Link: https://www.youtube.com/channel/UChYhUxkwDNialcxj-OFRcDw
-->
                             <div class="card form-group" style="width: 30rem">
                                 <img src="../vendor/img/<?php echo $row->t_pic;?>" class="card-img-top">
                                 <div class="card-body">
                                     <h5 class="card-title">Technician Picture</h5>
                                     <input type="file" class="btn btn-success" id="exampleInputEmail1" name="t_pic">
                                 </div>
                             </div>
                             <hr>
                             <button type="submit" name="update_tech" class="btn btn-success">Update Technician</button>
                         </form>
                         <!-- End Form-->
                         <?php }?>
                     </div>
                 </div>

                 <hr>


                 <!-- Sticky Footer -->
                 <?php include("vendor/inc/footer.php");?>

             </div>
             <!-- /.content-wrapper -->

         </div>
         <!-- /#wrapper -->
         <!-- Author By: MH RONY
Author Website: https://developerrony.com
Github Link: https://github.com/dev-mhrony
Youtube Link: https://www.youtube.com/channel/UChYhUxkwDNialcxj-OFRcDw
-->
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
         <!-- Author By: MH RONY
Author Website: https://developerrony.com
Github Link: https://github.com/dev-mhrony
Youtube Link: https://www.youtube.com/channel/UChYhUxkwDNialcxj-OFRcDw
-->
         <!-- Bootstrap core JavaScript-->
         <script src="vendor/jquery/jquery.min.js"></script>
         <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

         <!-- Core plugin JavaScript-->
         <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

         <!-- Page level plugin JavaScript-->
         <script src="vendor/chart.js/Chart.min.js"></script>
         <script src="vendor/datatables/jquery.dataTables.js"></script>
         <script src="vendor/datatables/dataTables.bootstrap4.js"></script>

         <!-- Custom scripts for all pages-->
         <script src="vendor/js/sb-admin.min.js"></script>

         <!-- Demo scripts for this page-->
         <script src="vendor/js/demo/datatables-demo.js"></script>
         <script src="vendor/js/demo/chart-area-demo.js"></script>
         <!--INject Sweet alert js-->
         <script src="vendor/js/swal.js"></script>

 </body>
 <!-- Author By: MH RONY
Author Website: https://developerrony.com
Github Link: https://github.com/dev-mhrony
Youtube Link: https://www.youtube.com/channel/UChYhUxkwDNialcxj-OFRcDw
-->

 </html>