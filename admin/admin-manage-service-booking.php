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
                <?php if(isset($_SESSION['delete_success'])) {?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo $_SESSION['delete_success']; unset($_SESSION['delete_success']); ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <?php } ?>
                <?php if(isset($_SESSION['delete_error'])) {?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $_SESSION['delete_error']; unset($_SESSION['delete_error']); ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <?php } ?>
                <!-- Breadcrumbs-->
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="#">Service Bookings</a>
                    </li>
                    <li class="breadcrumb-item active">Manage</li>
                </ol>

                 <!--Service Bookings-->
                 <div class="card mb-3">
                     <div class="card-header d-flex align-items-center justify-content-between">
                         <div>
                             <i class="fas fa-table"></i>
                             Service Bookings
                         </div>
                         <?php
                             // Read filters from query params
                             $status = isset($_GET['status']) ? strtolower(trim($_GET['status'])) : 'all';
                             $q = isset($_GET['q']) ? trim($_GET['q']) : '';
                             $validStatuses = ['all','pending','approved','in progress','completed','cancelled'];
                             if (!in_array($status, $validStatuses)) { $status = 'all'; }
                         ?>
                         <div class="d-none d-md-flex align-items-center">
                             <div class="btn-group mr-2" role="group" aria-label="Status filters">
                                 <a href="admin-manage-service-booking.php?status=all" class="btn btn-outline-secondary <?php echo ($status==='all'?'active':''); ?>">All</a>
                                 <a href="admin-manage-service-booking.php?status=pending" class="btn btn-outline-secondary <?php echo ($status==='pending'?'active':''); ?>">Pending</a>
                                 <a href="admin-manage-service-booking.php?status=approved" class="btn btn-outline-secondary <?php echo ($status==='approved'?'active':''); ?>">Approved</a>
                                 <a href="admin-manage-service-booking.php?status=in%20progress" class="btn btn-outline-secondary <?php echo ($status==='in progress'?'active':''); ?>">In Progress</a>
                                 <a href="admin-manage-service-booking.php?status=completed" class="btn btn-outline-secondary <?php echo ($status==='completed'?'active':''); ?>">Completed</a>
                                 <a href="admin-manage-service-booking.php?status=cancelled" class="btn btn-outline-secondary <?php echo ($status==='cancelled'?'active':''); ?>">Cancelled</a>
                             </div>
                             <form class="form-inline" method="get" action="admin-manage-service-booking.php">
                                 <input type="hidden" name="status" value="<?php echo htmlspecialchars($status); ?>">
                                 <input class="form-control mr-2" type="search" name="q" placeholder="Search bookings..." value="<?php echo htmlspecialchars($q); ?>">
                                 <button class="btn btn-primary" type="submit">Search</button>
                                 <?php if($q){ ?>
                                     <a class="btn btn-link" href="admin-manage-service-booking.php?status=<?php echo urlencode($status); ?>">Clear</a>
                                 <?php } ?>
                             </form>
                         </div>
                     </div>
                     <div class="card-body">
                         <div class="d-md-none mb-3">
                             <div class="btn-group mb-2" role="group" aria-label="Status filters">
                                 <a href="admin-manage-service-booking.php?status=all" class="btn btn-outline-secondary <?php echo ($status==='all'?'active':''); ?>">All</a>
                                 <a href="admin-manage-service-booking.php?status=pending" class="btn btn-outline-secondary <?php echo ($status==='pending'?'active':''); ?>">Pending</a>
                                 <a href="admin-manage-service-booking.php?status=approved" class="btn btn-outline-secondary <?php echo ($status==='approved'?'active':''); ?>">Approved</a>
                                 <a href="admin-manage-service-booking.php?status=in%20progress" class="btn btn-outline-secondary <?php echo ($status==='in progress'?'active':''); ?>">In Progress</a>
                                 <a href="admin-manage-service-booking.php?status=completed" class="btn btn-outline-secondary <?php echo ($status==='completed'?'active':''); ?>">Completed</a>
                                 <a href="admin-manage-service-booking.php?status=cancelled" class="btn btn-outline-secondary <?php echo ($status==='cancelled'?'active':''); ?>">Cancelled</a>
                             </div>
                             <form class="form-inline" method="get" action="admin-manage-service-booking.php">
                                 <input type="hidden" name="status" value="<?php echo htmlspecialchars($status); ?>">
                                 <input class="form-control mr-2" type="search" name="q" placeholder="Search bookings..." value="<?php echo htmlspecialchars($q); ?>">
                                 <button class="btn btn-primary" type="submit">Search</button>
                                 <?php if($q){ ?>
                                     <a class="btn btn-link" href="admin-manage-service-booking.php?status=<?php echo urlencode($status); ?>">Clear</a>
                                 <?php } ?>
                             </form>
                         </div>
                         <div class="table-responsive">
                             <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                 <thead>
                                     <tr>
                                         <th>#</th>
                                         <th>Customer Name</th>
                                         <th>Service</th>
                                         <th>Booking Date</th>
                                         <th>Booking Time</th>
                                         <th>Assigned Technician</th>
                                         <th>Status</th>
                                         <th>Action</th>
                                     </tr>
                                 </thead>
                                 <tbody>
                                     <?php
                  // Build dynamic query based on filters
                  $sql = "SELECT sb.*, u.u_fname, u.u_lname, s.s_name, t.t_name as tech_name 
                          FROM tms_service_booking sb
                          LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
                          LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
                          LEFT JOIN tms_technician t ON sb.sb_technician_id = t.t_id";

                  $where = [];
                  $params = [];
                  $types = '';

                  if ($status !== 'all') {
                      // Normalize status to match DB values capitalization
                      $statusValue = ucwords($status);
                      // Handle "In Progress" properly
                      if ($status === 'in progress') { $statusValue = 'In Progress'; }
                      $where[] = 'sb.sb_status = ?';
                      $params[] = $statusValue;
                      $types .= 's';
                  }

                  if ($q !== '') {
                      $like = '%' . $q . '%';
                      $where[] = '(CONCAT(u.u_fname, " ", u.u_lname) LIKE ? OR s.s_name LIKE ? OR t.t_name LIKE ? OR sb.sb_booking_date LIKE ? OR sb.sb_booking_time LIKE ?)';
                      array_push($params, $like, $like, $like, $like, $like);
                      $types .= 'sssss';
                  }

                  if (!empty($where)) {
                      $sql .= ' WHERE ' . implode(' AND ', $where);
                  }
                  $sql .= ' ORDER BY sb.sb_created_at DESC';

                  $stmt = $mysqli->prepare($sql);
                  if (!empty($params)) {
                      $stmt->bind_param($types, ...$params);
                  }
                  $stmt->execute();
                  $res = $stmt->get_result();
                  $cnt=1;
                  
                  if($res->num_rows == 0) {
                      echo '<tr><td colspan="8" class="text-center">No service bookings found.</td></tr>';
                  } else {
                      while($row=$res->fetch_object())
                      {
                  ?>
                                     <tr>
                                         <td><?php echo $cnt;?></td>
                                         <td><?php echo htmlspecialchars($row->u_fname . ' ' . $row->u_lname);?></td>
                                         <td><?php echo htmlspecialchars($row->s_name);?></td>
                                         <td><?php echo date('M d, Y', strtotime($row->sb_booking_date));?></td>
                                         <td><?php echo date('h:i A', strtotime($row->sb_booking_time));?></td>
                                         <td><?php echo $row->tech_name ? htmlspecialchars($row->tech_name) : '<span class="badge badge-warning">Not Assigned</span>';?></td>
                                         <td>
                                             <?php 
                                             if($row->sb_status == "Pending"){ 
                                                 echo '<span class="badge badge-warning">'.$row->sb_status.'</span>'; 
                                             } elseif($row->sb_status == "Approved"){ 
                                                 echo '<span class="badge badge-info">'.$row->sb_status.'</span>'; 
                                             } elseif($row->sb_status == "In Progress"){ 
                                                 echo '<span class="badge badge-primary">'.$row->sb_status.'</span>'; 
                                             } elseif($row->sb_status == "Completed"){ 
                                                 echo '<span class="badge badge-success">'.$row->sb_status.'</span>'; 
                                             } else { 
                                                 echo '<span class="badge badge-danger">'.$row->sb_status.'</span>'; 
                                             }
                                             ?>
                                         </td>
                                         <td>
                                             <a href="admin-view-service-booking.php?sb_id=<?php echo $row->sb_id;?>" class="badge badge-info">View</a>
                                             <?php if($row->sb_status != 'Cancelled' && $row->sb_status != 'Completed'): ?>
                                             <a href="admin-assign-technician.php?sb_id=<?php echo $row->sb_id;?>" class="badge badge-success">Assign</a>
                                             <a href="admin-cancel-service-booking.php?sb_id=<?php echo $row->sb_id;?>" class="badge badge-warning" onclick="return confirm('Cancel this booking?');">Cancel</a>
                                             <?php endif; ?>
                                             <a href="admin-delete-service-booking.php?sb_id=<?php echo $row->sb_id;?>" class="badge badge-danger" onclick="return confirm('Delete permanently?');">Delete</a>
                                         </td>
                                     </tr>
                                     <?php $cnt = $cnt+1; 
                      }
                  }?>

                                 </tbody>
                             </table>
                         </div>
                     </div>
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
     <script>
       // Apply client-side status filter based on URL param as a fallback
       $(function(){
         var table = $('#dataTable').DataTable();
         var status = '<?php echo htmlspecialchars($status); ?>';
         var map = {
           'pending': 'Pending',
           'approved': 'Approved',
           'in progress': 'In Progress',
           'completed': 'Completed',
           'cancelled': 'Cancelled',
           'all': null
         };
         var display = map[status] || null;
         if (display) {
           table.column(6).search(display).draw();
         } else {
           table.column(6).search('').draw();
         }

         // Wire search form to DataTables as well (without reload)
         var $searchInput = $('input[name="q"]');
         if ($searchInput.length) {
           $searchInput.on('keyup change', function(){
             table.search(this.value).draw();
           });
         }
       });
     </script>
 </body>

 </html>

